# Cliente OpenId

## Instalação

Para instalar pelo Composer, primeiro execute:

``` bash
composer config repositories.auth '{"type":"vcs", "url":"http://bitbucket.univicosa.com.br:7990/scm/auth/openid-client.git"}'
```

Esse comando adiciona o repositório do Bitbucket ao `composer.json`.

Em seguida execute o seguinte comando:

``` bash
composer config secure-http false
```

Esse comando configura o composer para aceitar conexões via `http`.

Por fim execute o comando `require` a seguir:

```bash
composer require auth/openid-client
```

### Adicionar o _Service Provider_

Em seguida registre o _service provider_ no arquivo `config/app.php`.

```php
'providers' => [
    
    /*
     *    ...
     */
     
    Modules\OpenId\Providers\OpenIdServiceProvider::class,
    
    /*
     *    ...
     */
],
```

Para publicar o arquivo de configuração execute o seguinte comando:

```bash
php artisan vendor:publish --tag=openid-config
```

O arquivo de configuração `config/openid.php` será gerado.

### Redirecionando para o login

No arquivo `app\Exceptions\Handler.php` procurar pelo método `unauthenticated` (pode estar na classe pai, nesse caso basta sobreescrevê-lo), altere a rota do redirecionamento para:

```php
    config('server') . '/login?continue=' . env('APP_URL')
```

**Obs.:** Não esquecer de alterar a duração da sessão para 240 minutos.