<?php
use PhpPusher\Server;

require_once __DIR__ . "/../../vendor/autoload.php";

class PusherServer extends Server
{
    public $key     = 'Password';
    public $port    = 8080;

    public function authLogin($client) {
        $cookies = $client->WebSocket->request->getCookies();
        // return false if the client has no session
        if(!isset($cookies['session']))
            return false;
        $session = $cookies['session'];
        if(!$session)
            return false;
        // Return the user id
        return 5;
        //return getUserIdBySession(urldecode($session));
    }

    public function authAdmin($client) {
        $admin_ids = [1,5,9];
        if(in_array($client->login, $admin_ids))
            return true;
        return false;
    }
}

$server = new PusherServer();
$server->setConfig(require('config.php'));
$server->run();
