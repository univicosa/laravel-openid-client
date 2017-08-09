<?php


namespace Modules\OpenId\Repositories;


class Client
{
    /**
     * @return \GuzzleHttp\Client
     */
    public static function getClient()
    {
        return new \GuzzleHttp\Client([
            'base_uri' => config('openid.server'),
            'headers' => [
                'Authorization' => 'Bearer ' . session('access_token')
            ]
        ]);
    }
}