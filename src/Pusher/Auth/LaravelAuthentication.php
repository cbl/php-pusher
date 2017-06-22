<?php
namespace PhpPusher\Auth;

use App\User;

/**
 * Handle authentication in Laravel.
 */
class LaravelAuthentication
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
        return $client->session->get(LaravelAuth::getName());
    }

    /**
     * Laravel admin authentication
     *
     * @param class     $client     The client that should be authenticated
     */
    public function authAdmin($client) {
        return User::where('id', $client->login)->first()->admin;
    }
}
