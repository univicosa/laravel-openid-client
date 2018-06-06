# Univiçosa Laravel OpenId Client

| **Laravel**  |  **laravel-openid-client** |
|------|------|
| 5.4  | ^0.7.3  |
| 5.5  | ^0.7.3  |
| 5.6  | ^0.7.3  |

`univicosa/laravel-openid-client` is a Laravel package which created to integrate the Oauth server to ours Laravel project's that requires authentication.

## Install

Installation using composer:

```
composer require univicosa/laravel-openid-client
```

For laravel versions < 5.5 add the service provider in `config/app.php`:

```
Modules\OpenId\Providers\OpenIdServiceProvider::class
```

To personalize the config, publish the package's configuration file by running:

```
php artisan vendor:publish --tag=openid-config
```

The file `config/openid.php` will be generated.

## Redirecting to _Login_

In the file `app\Exceptions\Handler.php` find or overwrite the `unauthenticated` method and change the redirect route to:

```php
config('openid.server') . '/login?continue=' . env('APP_URL')
```

**PS:** Don't forget change the `SESSION_LIFETIME` in the .env file to the time in minutes you want to keep the logged session.

## For change the _Guard_

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

## Oauth `public key`

Copy the `oauth public key` to `storage` folder of your project.

## _Facades_

The client methods are available under the facade OpenId.

The authentication methods like the verifier `\Auth::check()` are available under the Facade `\Illuminate\Support\Facades\Auth`;

The facade Oauth2 provides all helpers needed to get and post data from the Oauth Server.

## _View components_

`@openidComponents`:

For load the user's logged menu, the fast access with the users permissions and render the Login Button in case you have not authenticated page, just call the Blade directive under your header component.

`@login('route.name')`:

The login directive will call the route you pass or return the oauth path formated with the continue parameter to the required route.

## _Redirecting routes_

The dynamic route from Oauth system can redirect the user back to the source using the `?continue` url parameter.

The following example will be redirect back to the source after the user executes the actions needed in the Oauth Service page:

```php
config('openid.server') . '{$ouath_service_page}?' . http_build_query(['continue' => {$route_to_redirect_back}])
```