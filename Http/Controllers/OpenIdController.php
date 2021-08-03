<?php

namespace Modules\OpenId\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

class OpenIdController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        $continue = $request->get('continue',null);

        if ($request->has('error')) {
            switch ($request->get('error')) {
                case 'access_denied':
                    abort(401);
                    break;
                case 'temporarily_unavailable':
                    abort(503);
                    break;
                case 'invalid_request':
                case 'unauthorized_client':
                case 'unsupported_response_type':
                case 'invalid_scope':
                case 'server_error':
                default:
                    abort($request->server->get('REDIRECT_STATUS', 500));
                    break;
            }
        }

        $code = $request->get('code');
        $http = new Client();

        try {
            $response = $http->post(config('openid.server') . '/oauth/token', [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => config('openid.client.id'),
                    'client_secret' => config('openid.client.secret'),
                    'redirect_uri'  => route('openid.callback'),
                    'code'          => $code,
                ],
            ]);

            $this->checkError($response);
            $response = json_decode($response->getBody());

            session([
                'openid_token'  => $response->id_token,
                'access_token'  => $response->access_token,
                'refresh_token' => $response->refresh_token,
                'expires_at'    => Carbon::now()->addSeconds($response->expires_in),
            ]);
        } catch (\Exception $exception) {
            if ($exception instanceof ServerException || $exception instanceof ClientException) {
                $this->checkError($exception->getResponse());
            }

            abort($exception->getCode() !== 0 ? $exception->getCode() : 500);
        }

        return redirect()->intended(parse_url($continue)['path']);
    }

    /**
     * @param ResponseInterface $response
     */
    protected function checkError(ResponseInterface $response)
    {
        $result = json_decode($response->getBody());

        if (isset($result->error)) {
            switch ($result->error) {
                case 'invalid_request':
                case 'invalid_client':
                case 'invalid_grant':
                case 'unauthorized_client':
                case 'unsupported_grant_type':
                case 'invalid_scope':
                default:
                    abort($response->getStatusCode());
                    break;
            }
        }
    }
}
