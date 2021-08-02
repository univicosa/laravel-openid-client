<?php

namespace Modules\OpenId\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class LoginController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login()
    {
        $query = http_build_query([
            'continue'      => Input::get('continue'),
            'client_id'     => config('openid.client.id'),
            'redirect_uri'  => route('openid.callback'),
            'response_type' => 'code',
            'scope'         => 'openid',
        ]);

        return redirect(config('openid.server') . "/oauth/authorize?$query");
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::guard()->logout();
            Request::session()->invalidate();
        }

        return response()->json(['success' => TRUE]);
    }
}
