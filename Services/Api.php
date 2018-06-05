<?php
/**
 * Created by Olimar Ferraz
 * Email: olimarferraz@univicosa.com.br
 * Date: 05/06/2018 - 14:17
 */

namespace Modules\OpenId\Services;

class Api
{
    /**
     * @var \GuzzleHttp\Client
     */
    private static $client;
    /**
     * @var string
     */
    private static $version;
    /**
     * @var bool
     */
    private static $initialized = FALSE;

    /**
     * Class constructor
     */
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$client = \OpenId::getClient();
        self::$version = config('openid.api-version');
        self::$initialized = TRUE;
    }

    /**
     * @return array
     */
    public static function getSystems()
    {
        return self::getResponse('system');
    }

    /**
     * @return array
     */
    public static function getSystemRoles()
    {
        return self::getResponse('system/roles');
    }

    /**
     * @return array
     */
    public static function getUser()
    {
        return self::getResponse('user');
    }

    /**
     * @return array
     */
    public static function getUserSystems()
    {
        return self::getResponse('user/systems');
    }

    /**
     * @return array
     */
    public static function getUserPermissions()
    {
        return self::getResponse('user/permissions');
    }

    /**
     *
     */
    public static function createUserPermission()
    {
        //
    }

    /**
     * @return array
     */
    public static function getStates()
    {
        return self::getResponse('address/states');
    }

    /**
     * @param string $state
     *
     * @return array
     */
    public static function getCities(string $state)
    {
        return self::getResponse('address/cities/' . $state);
    }

    /**
     * @return array
     */
    public static function isAddressFilled()
    {
        return self::getResponse('address/filled');
    }

    /**
     * @param string $uri
     *
     * @return array
     */
    private static function getResponse(string $uri): array
    {
        self::initialize();

        $response = self::$client->get('api/' . self::$version . '/' . $uri);

        return json_decode($response->getBody(), TRUE);
    }
}