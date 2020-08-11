<?php

namespace Modules\OpenId\Services;

class Client
{
    /**
     * @return \GuzzleHttp\Client
     */
    public static function getClient()
    {
        return new \GuzzleHttp\Client([
            'base_uri' => config('openid.server'),
            'headers'  => [
                'Authorization' => 'Bearer ' . session('access_token'),
                'Accept'        => 'application/json',
            ],
        ]);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public static function getServerClient()
    {
        return new \GuzzleHttp\Client([
            'base_uri' => config('openid.server'),
            'headers'  => [
                'ClientId'     => config('openid.client.id'),
                'ClientSecret' => config('openid.client.secret'),
                'Accept'       => 'application/json',
            ],
        ]);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function createUser(array $data): array
    {
        $client = self::getServerClient();
        $params = array_only($data, ['name', 'email', 'cpf', 'setProfile']);

        $response = $client->post('api/' . config('openid.api-version') . '/user', [
            'form_params' => $params,
        ]);

        return json_decode($response->getBody(), TRUE);
    }
}