<?php
namespace PhpPusher\Auth;

use App;
use Auth;
use Config;
use Crypt;

use Illuminate\Session\SessionManager;

/**
 * Handle authentication in Laravel.
 */
trait LaravelAuthentication
{
    /**
     * Laravel login
     *
     * @param class     $client     The client that should be authenticated
     */
    public function authLogin($client) {
        // get cookie
        $cookie = $client->WebSocket->request->getCookie(config('session.cookie'));
        if(!$cookie)
            $cookie = $client->WebSocket->request->getQuery('session');
        if(!$cookie)
            return false;
        // get session id
        $session_cookies    = urldecode($cookie);
        $session            = (new SessionManager(App::getInstance()))->driver();
        $session_id         = Crypt::decrypt($session_cookies);
        $session->setId($session_id);
        $client->session = $session;
        $client->session->start();
        // return user id
        return $client->session->get(Auth::getName());
    }

    /**
     * Laravel admin authentication
     *
     * @param class     $client     The client that should be authenticated
     */
    public function authAdmin($client) {
        if(isset($this->user_model))
            $user = $this->user_model;
        else
            $user = App\User::class;
        return $user::where('id', $client->login)->first()->admin;
    }
}
