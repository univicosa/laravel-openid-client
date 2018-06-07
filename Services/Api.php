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
     * Static class constructor
     */
    private static function initialize()
    {
        if (self::$initialized) return;

        self::$client = \OpenId::getClient();
        self::$version = config('openid.api-version');
        self::$initialized = TRUE;
    }

    /**
     * @api GET '/api/{version}/system'
     *
     * @return array With Systems available in Oauth Server
     */
    public static function getSystems()
    {
        return self::getResponse('system');
    }

    /**
     * @api GET '/api/{version}/system/roles'
     *
     * @return array With System givable roles
     */
    public static function getSystemRoles()
    {
        return self::getResponse('system/roles');
    }

    /**
     * @api GET '/api/{version}/user'
     *
     * @return array With logged user data
     */
    public static function getUser()
    {
        return self::getResponse('user');
    }

    /**
     * @api POST '/api/{version}/user/cpf'
     *
     * @param string $cpf
     *
     * @return array With the data of user owner of document given
     */
    public static function getUserByCpf(string $cpf)
    {
        return self::getResponse('user/cpf', ['cpf' => $cpf]);
    }

    /**
     * @api GET '/api/{version}/user/systems'
     *
     * @return array With the systems that the user is allowed to access
     */
    public static function getUserSystems()
    {
        return self::getResponse('user/systems');
    }

    /**
     * @api GET '/api/{version}/user/permissions'
     *
     * @return array With the roles that the logged user has in the request owner
     */
    public static function getUserPermissions()
    {
        return self::getResponse('user/permissions');
    }

    /**
     * @api GET '/api/{version}/user/genders'
     *
     * @return array With the data of all genders available for select on the Oauth Server
     */
    public static function getGenders()
    {
        return self::getResponse('profile/genders');
    }

    /**
     * @api POST '/api/{version}/user/permission'
     *
     * @param string $cpf
     * @param string $role
     * @param string $expires_at
     *
     * @return array with the response of Post action
     */
    public static function setUserPermission(string $cpf, string $role, string $expires_at = '')
    {
        $params = [
            'user' => $cpf,
            'role' => $role,
        ];

        if ($expires_at !== '') $params['expires_at'] = date('Y-m-d', strtotime($expires_at));

        return self::postData('user/permission', $params);
    }

    /**
     * @api POST '/api/{version}/user/profile'
     *
     * @param array $data Requires 'name', 'social_name', 'gender', 'birthday_date', 'identity' and 'phones' keys
     *
     * @return array with the response of Post action
     */
    public static function setProfile(array $data)
    {
        $params = array_only($data, ['name', 'social_name', 'gender', 'birthday_date', 'identity', 'phones']);

        return self::postData('profile', $params);
    }

    /**
     * @api GET '/api/{version}/address/states'
     *
     * @return array With the data of all Brazilian states present on the Oauth Server
     */
    public static function getStates()
    {
        return self::getResponse('address/states');
    }

    /**
     * @api GET '/api/{version}/address/cities/{state}'
     *
     * @param string $state
     *
     * @return array With the data of all Brazilian cities according to the state given present on the Oauth Server
     */
    public static function getCities(string $state)
    {
        return self::getResponse('address/cities/' . $state);
    }

    /**
     * @api GET '/api/{version}/address/filled'
     *
     * @return array With the Boolean response if the user address data is populated on the Oauth Server
     */
    public static function isAddressFilled()
    {
        return self::getResponse('address/filled');
    }

    /**
     * @api POST '/api/{version}/address'
     *
     * @param array $data Requires 'zip', 'street', 'number', 'complement', 'district' and 'city' keys
     *
     * @return array with the response of Post action
     */
    public static function setAddress(array $data)
    {
        $params = array_only($data, ['zip', 'street', 'number', 'complement', 'district', 'city']);

        return self::postData('address', $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    private static function getResponse(string $uri, array $params = []): array
    {
        self::initialize();

        if (empty($params)) {
            $response = self::$client->get('api/' . self::$version . '/' . $uri);
        } else {
            $response = self::$client->post('api/' . self::$version . '/' . $uri, [
                'form_params' => $params,
            ]);
        }

        return json_decode($response->getBody(), TRUE);
    }

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return array
     */
    private static function postData(string $uri, array $data): array
    {
        self::initialize();

        $response = self::$client->post('api/' . self::$version . '/' . $uri, [
            'form_params' => $data,
        ]);

        return json_decode($response->getBody(), TRUE);
    }
}