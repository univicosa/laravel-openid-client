<?php
/**
 * Created by Olimar Ferraz
 * Email: olimarferraz@univicosa.com.br
 * Date: 04/06/2018 - 14:35
 */

namespace Modules\OpenId\Http\ViewComposers;


use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserSystemsComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        if (\Auth::check()) {
            $view->with('avatar', \Auth::user()->avatar);
            $view->with('firstName', $this->name(TRUE));
            $view->with('fullName', $this->name());
            $view->with('email', $this->email());
            $view->with('greeting', $this->greeting() . ', ');
            $view->with('systems', $this->systems());
            $view->with('logout', config('openid.server') . '/logout?continue=' . env('APP_URL'));
        } else {
            $view->with('login', config('openid.server') . '/login?' . http_build_query(['continue' => env('APP_URL')]));
        }
    }
    
    /**
     * @param bool $first
     *
     * @return string
     */
    private function name($first = FALSE): string
    {
        $user = \Oauth2::getUser()["user"];

        if ($user['social_name']) {
            return $user['social_name'];
        }

        $name = isset(\Auth::user()->name) ? \Auth::user()->name : 'John Doe';

        if ($first) return explode(' ', $name)[0];

        return $name;
    }

    /**
     * @return string
     */
    private function email(): string
    {
        return isset(\Auth::user()->email) ? \Auth::user()->email : __('undefined e-mail');
    }

    /**
     * @return string
     */
    private function greeting(): string
    {
        if (date('H') < 12) return __('openid::greeting.good_morning');
        if (date('H') < 18) return __('openid::greeting.good_afternoon');
        if (date('H') <= 23) return __('openid::greeting.good_night');

        return __('openid::greeting.hello');
    }

    /**
     * @return array
     */
    private function systems(): array
    {
        if (!\Session::has('systems') && Auth::check()) {
            \Session::put(\Oauth2::getUserSystems());
        }

        return \Session::get('systems');
    }
}