<?php

namespace PhpPusher\Cache;

trait ClientHandler
{
    protected $clients = [];

    protected function sendEverything($client) {
        $message = $this->filterCacheByClient($client);
        if(!empty($message))
            $client->send(json_encode($message));
    }

    protected function sendRowToAll($type, $name, $data) {
        $config = $this->config[$type][$name];
        // Check if client uses the right url
        if($config['send_to'] != 'all' AND $client->url != $config['send_to'])
            return false;
        // Check Auth
        foreach($config['auth'] as &$auth) {
            if(!$client->{$auth})
                return false;
        }
        $message = $this->createMessage($type, $name, $data);
        if($config['save_auth']) {
            if($message['auth'] == $client->login)
                $message['auth'] = true;
            else
                unset($message['auth']);
        }
        $this->_sendAll($message);
    }

    protected function _sendAll($message) {
        foreach($this->clients as $client) {
            if(!$client->server)
                $client->send(json_encode([$message]));
        }
    }

    protected function sendAll($type, $name, $data, array $filter = []) {
        $this->_sendAll($this->createMessage($type, $name, $data));
    }
}
