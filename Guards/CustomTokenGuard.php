<?php

namespace Modules\OpenId\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Modules\OpenId\Entities\User;

class CustomTokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }
        $user = NULL;
        $token = $this->getTokenForRequest();
        if (!empty($token)) {
            $user = $this->retrieveByToken($token);
        }

        return $this->user = $user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return FALSE;
    }

    /**
     * @return null|string
     */
    private function getTokenForRequest()
    {
        return \Request::bearerToken();
    }

    /**
     * @param string $token
     *
     * @return User|null
     */
    private function retrieveByToken(string $token)
    {
        $token = $this->validateToken($token);
        if (!$token) {
            return NULL;
        }
        $user = new User();
        if ($token->hasClaim('sub')) {
            $user->id = $token->getClaim('sub');
        }
        if ($token->hasClaim('name')) {
            $user->name = $token->getClaim('name');
        }
        if ($token->hasClaim('email')) {
            $user->email = $token->getClaim('email');
        }
        if ($token->hasClaim('roles')) {
            $user->roles = explode(' ', $token->getClaim('roles'));
        }
        if ($token->hasClaim('registries')) {
            $user->registries = explode(' ', $token->getClaim('registries'));
        }
        if ($token->hasClaim('cpf')) {
            $user->cpf = $token->getClaim('cpf');
        }
        if ($token->hasClaim('avatar')) {
            $user->avatar = $token->getClaim('avatar');
        }

        return $user;
    }

    /**
     * @param string $id
     *
     * @return \Lcobucci\JWT\Token|null
     */
    private function validateToken(string $id)
    {
        $token = (new Parser())->parse((string)$id);
        //Verifica se o token expirou
        if ($token->isExpired()) {
            return NULL;
        }
        //Verifica a assinatura
        $signer = new Sha256();
        $key = new Key('file://' . config('openid.key'));
        if (!$token->verify($signer, $key)) {
            return NULL;
        }
        //Verifica os dados
        $validation = new ValidationData();
        $validation->setIssuer(config('openid.server'));
        $validation->setAudience(config('openid.client.id'));
        if (!$token->validate($validation)) {
            return NULL;
        }

        return $token;
    }
}