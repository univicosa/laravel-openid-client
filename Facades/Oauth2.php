<?php
/**
 * Created by Olimar Ferraz
 * Email: olimarferraz@univicosa.com.br
 * Date: 05/06/2018 - 14:54
 */

namespace Modules\OpenId\Facades;


use Illuminate\Support\Facades\Facade;

class Oauth2 extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'oauth2';
    }
}