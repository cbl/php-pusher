<?php

namespace PhpPusher\Server;

trait MessageHandler
{
    protected function newMessage($client, $message) {
        // Check if client is Server
        if(!$client->server)
            return false;
        // Try to decode Message
        $message = $this->decode($message);
        if(!$message)
            return false;
        // Check if Message strucuture is correct
        $required = ['type', 'name', 'data'];
        if(count(array_intersect_key(array_flip($required), $message)) !== count($required))
            return false;
        // Handle Message
        $this->handleMessage($client, $message);
    }

    private function handleMessage($client, $message) {
        $config = $this->getMessageConfig($message);
        if(!$config)
            return false;
        if($config['type'] == 'timer')
            $this->startTimer($client, $config['name'], $config['options']);
        if($config['type'] == 'list')
            $this->newData($client, 'list', $message, $config['options']);
        if($config['type'] == 'dict')
            $this->newData($client, 'dict', $message, $config['options']);
    }

    private function newData($client, $type, $message, $config) {
        if(!empty($config['auth'])) {
            if($config['auth']['login'] === true AND $config['save_auth'])
                $message['data']['auth'] = $client->login;
        }
        if($config['cache']) {
            if($type == 'list') {
                if(!isset($this->cache['list'][$message['name']]))
                    $this->cache['list'][$message['name']] = [];
                $this->cache['list'][$message['name']][] = $message['data'];
            } else if($type == 'dict') {
                $this->cache['dict'][$message['name']] = $message['data'];
            }
        }
        // Send
        $this->sendRowToAll($type, $message['name'], $message['data']);
    }


    private function getMessageConfig($message) {
        // Check if type is Timer
        if($message['type'] == 'timer')
            return $this->returnConfig('timer', $message['name'], $message['data']);
        // Check if type is List
        if(array_key_exists($message['name'], $this->config['list']))
            return $this->returnConfig('list', $message['name'], $this->config['list'][$message['name']]);
        // Check if type is Dictionary
        if(array_key_exists($message['name'], $this->config['dict']))
            return $this->returnConfig('dict', $message['name'], $this->config['dict'][$message['name']]);
        return false;
    }

    private function returnConfig($type, $name, $options) {
        return [
            'type'      => $type,
            'name'      => $name,
            'options'   => $options
        ];
    }

    /**
     * Try to Json decode message
     * @param   string $string  String that should be decoded
     * @return  array  $array   Decoded input
     */
    private function decode($string) {
        $array = @json_decode($string, true);
        if($array === null)
            return false;
        return $array;
    }
}
