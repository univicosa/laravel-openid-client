<?php

namespace Modules\OpenId\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(Request $request)
    {
        $continue = \Request::query('continue', null);

        $query = http_build_query([
            'continue' => $continue,
            'client_id' => config('openid.client.id'),
            'redirect_uri' => route('openid.callback'),
            'response_type' => 'code',
            'scope' => 'openid',
        ]);

        return redirect(config('openid.server') . "/oauth/authorize?$query");
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        /* if (Auth::check()) {
             Auth::guard()->logout();
             Request::session()->invalidate();
         }
 */
        // return response()->json(data: ['success' => TRUE]);
        if (Auth::check()) {
            Auth::logout();
            Auth::guard()->logout();

            //   $request->session()->invalidate();
            //   $request->session()->flush();


            // Session::flush();

        }
        return response()->json(['success' => TRUE]);

        //return redirect(config('openid.server') . '/login?continue=' . env('APP_URL'))->withCookie($cookie);
    }
}
