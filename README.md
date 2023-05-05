# Univiçosa Laravel OpenId Client

`univicosa/laravel-openid-client` is a Laravel package which created to integrate the Oauth server to ours Laravel project's that requires authentication.

## Install

Installation using composer:

```
composer require univicosa/laravel-openid-client
```

For Laravel versions < 5.5 add the service provider in `config/app.php`:

```
Modules\OpenId\Providers\OpenIdServiceProvider::class
```

To personalize the config, publish the package's configuration file by running:

```
php artisan vendor:publish --tag=openid-config
```

The file `config/openid.php` will be generated.

### Oauth `public key`

Your system need the `oauth public key` to connect and communicate with the Oauth Server.So you need to copy the public key file to `storage` folder of your project.

### Redirecting to _Login_

In the file `app\Exceptions\Handler.php` find or overwrite the `unauthenticated` method and change the redirect route to:

```php
protected function unauthenticated($request, AuthenticationException $exception) : \Illuminate\Http\RedirectResponse
{
     if ($request->expectsJson()) {
         return response()->json(['error' => 'Unauthenticated.'], 401);
     }
     
     if (env('APP_ENV') === 'local') {
         return redirect()->guest(route('login'));
     }

     return redirect()->guest(config('openid.server') . '/login?continue=' . $request->url());
}
```

### .env File

Set a variable called `SESSION_LIFETIME` in the `.env` file and define it to the time in minutes you want to keep the logged session. The max time of the Oauth Server keeps the session is 240 minutes (4 hours).

```php
#### APP CONFIG ####
APP_URL=url-project (https://domain.com)

#### CACHE CONFIG ####
BROADCAST_DRIVER=log
CACHE_DRIVER=redis
SESSION_DRIVER=file
SESSION_LIFETIME=240
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

#### OAUTH OPENID ####
AUTH_SERVER=https://oauth.univicosa.com.br or https://devauth.univicosa.com.br
CLIENT_ID=you-client-id
CLIENT_SECRET=your-secret-code
```

### Change Kernel.php
In file *app/Http/Kernel.php** change the attribute $routeMiddleware

```php
protected $routeMiddleware = [
   'auth' => \Illuminate\Auth\Middleware\Authenticate::class // CHANGE THIS
]; 
```

### For change the _Guard_

change the file `config\auth.php` to:

```php
'guards' => [
    'web' => [
        'driver' => 'openid',
        'provider' => NULL,
    ],
    /*
     * ...
     */
]
```

## _Facades_

The client methods are available under the facade **\OpenId**.

The authentication methods like the verifier `\Auth::check()` are available under the Facade **\Illuminate\Support\Facades\Auth**;

The facade **\Oauth2** provides all helpers needed to get and post data from the Oauth Server.

### \Ouath2 methods available

```php
@method \Oauth2::getSystems(): array
@api GET '/api/{version}/system'

@return array With Systems available in Oauth Server
```

```php
@method \Oauth2::getSystemRoles(): array
@api GET '/api/{version}/system/roles'

@return array With System givable roles
```

```php
@method \Oauth2::getSystemPermissions(): array
@api GET '/api/{version}/system/permissions'

@return array With System givable permissions
```


```php
@method \Oauth2::getUser(): array
@api GET '/api/{version}/user'

@return array With logged user data
```

```php
@method \Oauth2::getUserProfiles(): array
@api GET '/api/{version}/profile'

@return array With logged user profiles
```

```php
@method \Oauth2::getUserByCpf(string $cpf): array
@api POST '/api/{version}/user/cpf'

@return array With the data of user owner of document given
```

```php
@method \Oauth2::getUsersByCpf(array $cpf): array
@api POST '/api/{version}/user/search/cpf'

@return array With the data of user owner of document given: limit array size to 100 items
```

```php
@method \Oauth2::searchUserByName(string $name): array
@api POST '/api/{version}/user/name'

@return array With the data of users owner of document given
```

```php
@method \Oauth2::searchUserByRegistry(string $registry): array
@api POST '/api/{version}/user/registry'

@return array With the data of users owner of registry given
```


```php
@method \Oauth2::getUserSystems(): array
@api GET '/api/{version}/user/systems'

@return array With the systems that the user is allowed to access
```

```php
@method \Oauth2::getUserPermissions(): array
@api GET '/api/{version}/user/permissions'

@return array With the roles that the logged user has in the request owner
```

```php
@method \Oauth2::getGenders(): array
@api GET '/api/{version}/profile/genders'

@return array With th data of all genders available for select on the Oauth Server
```

```php
@method \Oauth2::setUserPermission(string $cpf, string $role, string $expires_at = ''): array
@api POST '/api/{version}/user/permission'

@return array with the response of Post action
```

```php
@method \Oauth2::isAddressFilled(): array
@api GET '/api/{version}/address/filled'

@return array With the Boolean response if the user address data is populated on the Oauth Server
```

```php
@method \Oauth2::getStates(): array
@api GET '/api/{version}/address/states'

@return array With the data of all Brazilian states present on the Oauth Server
```

```php
@method \Oauth2::getCities(string $state): array
@api GET '/api/{version}/address/cities/{state}'

@return array With the data of all Brazilian cities according to the state given present on the Oauth Server
```

```php
@method \Oauth2::setAddress(array $data): array
@api POST '/api/{version}/address'

@return array with the response of Post action
```

```php
@method \Oauth2::setProfile(array $data): array
@api POST '/api/{version}/profile'

@return array with the response of Post action
```

```php
@method \Oauth2::setUsername(array $data): array
@api POST '/api/{version}/user/update/username'

@return array with the response of Post action
```

```php
@method \Oauth2::getUsersType(string $type): array
@api POST '/api/{version}/profile/users/type'

@return array With all user data available for a type selected on the Oauth server
```

```php
@method \Oauth2::setUsersType(array $data): array
@api POST '/api/{version}/profile/create/usertype'

@return array with the response of Post action
```

```php
@method \Oauth2::removeAllTypesFromUser(array $data): array
@api POST '/api/{version}/profile/remove/usertype'

@return array with the response of Post action
```

## _View components_

`@openidComponents`:

For load the user's logged menu, the fast access with the users permissions and render the Login Button in case you have not authenticated page, just call the Blade directive under your header component.

`@login('route.name')`:

The login directive will call the route you pass or return the oauth path formated with the continue parameter to the required route.

## _Redirecting routes_

The dynamic route from Oauth system can redirect the user back to the source using the `?continue` url parameter.

The following example will be redirect back to the source after the user executes the actions needed in the Oauth Service page:

```php
config('openid.server') . '{ouath_service_page}?' . http_build_query(['continue' => {route_to_redirect_back}])
```
