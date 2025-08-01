<?php

namespace Modules\OpenId\Facades;

use Illuminate\Support\Facades\Facade;

class OpenId extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'openid';
    }

}