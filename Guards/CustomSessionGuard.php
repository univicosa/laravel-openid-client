<?php

namespace Modules\OpenId\Guards;

use Carbon\Carbon;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Cookie\QueueingFactory as CookieJar;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Traits\Macroable;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Modules\OpenId\Entities\User;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class CustomSessionGuard implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * The user we last attempted to retrieve.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $lastAttempted;

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = FALSE;

    /**
     * The session used by the guard.
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    protected $session;

    /**
     * The Illuminate cookie creator service.
     *
     * @var \Illuminate\Contracts\Cookie\QueueingFactory
     */
    protected $cookie;

    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = FALSE;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $recallAttempted = FALSE;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Session\Session     $session
     * @param  \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Session $session,
                                Request $request = NULL)
    {
        $this->session = $session;
        $this->request = $request;
        $this->provider = NULL;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return NULL;
        }
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            if ($this->session->get('expires_at') < Carbon::now()) {
                $this->logout();

                return NULL;
            }

            return $this->user;
        }
        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        $id = $this->session->get($this->getName());
        $user = NULL;
        if (!is_null($id)) {
            $token = $this->validateToken($id);
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
            if ($user) {
                $this->fireAuthenticatedEvent($user);
            }
        }

        return $this->user = $user;
    }

    /**
     * @param string $id
     *
     * @return \Lcobucci\JWT\Token|null
     */
    public function validateToken(string $id)
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

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return string|null
     */
    public function id()
    {
        if ($this->loggedOut) {
            return NULL;
        }

        return $this->user()
            ? $this->user()->getAuthIdentifier()
            : $this->session->get($this->getName());
    }

    /**
     * Update the session with the given ID.
     *
     * @param  string $id
     *
     * @return void
     */
    protected function updateSession($id)
    {
        $this->session->put($this->getName(), $id);
        $this->session->migrate(TRUE);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();
        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();
        if (isset($this->events)) {
            $this->events->dispatch(new Logout($user));
        }
        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = NULL;
        $this->loggedOut = TRUE;
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->remove($this->getName());
    }

    /**
     * Fire the login event if the dispatcher is set.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  bool                                       $remember
     *
     * @return void
     */
    protected function fireLoginEvent($user, $remember = FALSE)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Login($user, $remember));
        }
    }

    /**
     * Fire the authenticated event if the dispatcher is set.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    protected function fireAuthenticatedEvent($user)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Authenticated($user));
        }
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param  array                                           $credentials
     *
     * @return void
     */
    protected function fireFailedEvent($user, array $credentials)
    {
        if (isset($this->events)) {
            $this->events->dispatch(new Failed($user, $credentials));
        }
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'openid_token';
    }

    /**
     * Get the cookie creator instance used by the guard.
     *
     * @return \Illuminate\Contracts\Cookie\QueueingFactory
     *
     * @throws \RuntimeException
     */
    public function getCookieJar()
    {
        if (!isset($this->cookie)) {
            throw new RuntimeException('Cookie jar has not been set.');
        }

        return $this->cookie;
    }

    /**
     * Set the cookie creator instance used by the guard.
     *
     * @param  \Illuminate\Contracts\Cookie\QueueingFactory $cookie
     *
     * @return void
     */
    public function setCookieJar(CookieJar $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function setDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Get the session store used by the guard.
     *
     * @return \Illuminate\Session\Store
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return \Illuminate\Contracts\Auth\UserProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider|null $provider
     */
    public function setProvider(UserProvider $provider = NULL)
    {
        $this->provider = $provider;
    }

    /**
     * Return the currently cached user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return $this
     */
    public function setUser(AuthenticatableContract $user)
    {
        $this->user = $user;
        $this->loggedOut = FALSE;
        $this->fireAuthenticatedEvent($user);

        return $this;
    }

    /**
     * Get the current request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
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
        return TRUE;
    }
}