<?php
namespace PhpPusher;

use WebSocket\Client as WebsocketClient;

class Client
{

    private $client;

    public function __construct($key, $session='',  $ip='127.0.0.1', $port=8080) {
        $uri = 'ws://'.$ip.':'.$port.'?key='.$key.'&session='.$session;
        $this->client = new WebsocketClient($uri);
    }

    public function send($name, $data) {
        $this->_send('data', $name, $data);
    }

    public function startTimer($name, $duration, $wait=true) {
        $this->_send('timer', $name, $duration);
        if($wait)
            return sleep($duration);
    }

    private function _send($type, $name, $data) {
        $message = [
            'type' => $type,
            'name' => $name,
            'data' => $data
        ];
        $this->client->send(json_encode($message));
    }
}
