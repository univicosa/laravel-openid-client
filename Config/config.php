<?php

return [
    /*
     | Caminho para o arquivo da chave pública
     */
    'key' => env('KEY_PATH', storage_path('oauth-public.key')),

    /*
     | URL do servidor OpenId
     */
    'server' => env('AUTH_SERVER', 'https://devauth.univicosa.com.br'),

    /*
     | Dados do cliente OpenId
     */
    'client' => [

        /*
         | Id do cliente
         */
        'id' => env('CLIENT_ID'),

        /*
         | Senha do cliente
         */
        'secret' => env('CLIENT_SECRET')
    ]

];
