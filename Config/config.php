<?php

return [
    'namespace' => 'openid',
    /*
     | path of the public key
     */
    'key'    => env('KEY_PATH', storage_path('oauth-public.key')),

    /*
     | OpenID server URL
     */
    'server' => env('AUTH_SERVER', 'https://devauth.univicosa.com.br'),

    /*
     | OpenID client data
     */
    'client' => [

        /*
         | Client ID
         */
        'id'     => env('CLIENT_ID'),

        /*
         | Client secret
         */
        'secret' => env('CLIENT_SECRET'),
    ],

    'api-version' => env('API_VERSION', 'v1'),
];
