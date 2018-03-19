# Univiçosa Laravel OpenId Client

## Install

Installation using composer:

```
composer require univicosa/laravel-openid-client
```

And add the service provider in config/app.php:

```
Modules\OpenId\Providers\OpenIdServiceProvider::class
```

Publish the package's configuration file by running:

```
php artisan vendor:publish --tag=openid-config
```

The file `config/openid.php` will be generated.

## Redirecting to _Login_

In the file `app\Exceptions\Handler.php` find or overwrite the `unauthenticated` method and change de redirect route to:

```php
    config('server') . '/login?continue=' . env('APP_URL')
```

**PS:** Don't forget change the session time to (minutes) you want.

## Changing the _Guard_

change the file `config\auth.php` to:

```
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

## Facades

The client methods are available under the facade OpenId