<?php
namespace PhpPusher\Auth;

trait AuthenticationHandler
{
    /**
     * Authenticate new Client
     */
    protected function authenticate($client) {
        $client->login  = false;
        $client->admin  = false;
        $client->server = $this->authServer($client);
        $client->url    = $this->getUrl($client);
        return $client;
    }

    /**
     * Get Client Url.
     */
    private function getUrl($client) {
        parse_str($client->WebSocket->request->getQuery(), $query);
        return (isset($query['page'])) ? $query['page'] : '';
    }

    /**
     * Check if Client is Server.
     */
    private function authServer($client) {
        parse_str($client->WebSocket->request->getQuery(), $query);
        if(!isset($query['key']))
            return false;
        if($this->key == $query['key'])
            return true;
        return false;
    }
}
