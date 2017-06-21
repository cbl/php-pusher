<?php
namespace PhpPusher\Auth;

/**
 * Handle authentications of a new client.
 */
trait AuthenticationHandler
{
    /**
     * Check authenticateions of new client.
     *
     * @param   class   $client     New connected client
     * @return  class   $client     New connected client with checked authentications
     * - int    $client->login  Login user id of the client, false if authentication failed
     * - bool   $client->admin  Is user admin
     * - bool   $client->server Is client server
     * - string $client->url    Url of the client
     */
    protected function authenticate($client) {
        $client->login  = false;
        $client->admin  = false;
        if(method_exists($this->ratchet, 'authLogin'))
            $client->login = $this->ratchet->authLogin($client);
        if(method_exists($this->ratchet, 'authAdmin'))
            $client->admin = $this->ratchet->authAdmin($client);
        // Auth server
        $client->server = $this->authServer($client);
        // Get client url
        $client->url    = $this->getUrl($client);
        return $client;
    }

    /**
     * Get client url.
     *
     * @param   class   $client     New connected client
     * @return  string  $url        Url of the connected client
     */
    private function getUrl($client) {
        parse_str($client->WebSocket->request->getQuery(), $query);
        return (isset($query['page'])) ? $query['page'] : '';
    }

    /**
     * Check if client is server.
     *
     * @param   class     $client   New connected client
     * @return  bool      $server   Is client server
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
