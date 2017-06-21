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

    public function send($name, $data, $receiver=false) {
        $this->sendData('data', $name, $data, $receiver);
    }

    public function startTimer($name, $duration, $wait=true) {
        $this->sendData('timer', $name, $duration);
        if($wait)
            sleep($duration + 1);
    }

    private function sendData($type, $name, $data, $receiver=false) {
        $message = [
            'type' => $type,
            'name' => $name,
            'data' => $data
        ];
        if($receiver)
            $message['receiver'] = $receiver;
        $this->client->send(json_encode($message));
    }
}
