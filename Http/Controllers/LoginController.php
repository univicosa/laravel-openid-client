<?php

namespace Modules\OpenId\Http\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class LoginController extends Controller
{
    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth')->only('logout');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login()
    {
        $query = http_build_query([
            'client_id' => config('openid.client.id'),
            'redirect_uri' => route('openid.callback'),
            'response_type' => 'code',
            'scope' => 'openid'
        ]);

        return redirect(config('openid.server') . "/oauth/authorize?$query");
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard()->logout();

        Request::session()->invalidate();

        return response()->json('success');
    }
}
