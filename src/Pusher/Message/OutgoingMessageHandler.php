<?php

namespace PhpPusher\Message;

/**
 * Handle outgoing messages.
 */
trait OutGoingMessageHandler
{
    /**
     * Send all data to the new connected client
     *
     * @param string    $client New connected client
     */
    protected function sendAllDataToNewClient($client) {
        $message = $this->filterCacheByClient($client);
        if(!empty($message))
            $client->send(json_encode($message));
    }

    /**
     * Send new data to all clients
     *
     * @param class     $client From client
     * @param string    $type   Data type ('list' or 'dict')
     * @param string    $name   Name of the data
     * @param data      $data   Data to send to the clients
     */
    protected function sendNewDataToAll($client, $type, $name, $data) {
        // Get Config
        $config = $this->config[$type][$name];
        // Check if client uses the right url
        if($config['send_to'] != 'all' AND $client->url != $config['send_to'])
            return false;
        // Create outgoing message
        $message = $this->createMessage($type, $name, $data);
        // Send message to all connected clients
        $this->sendAll($client, $message, $config);
    }

    /**
     * Filter cached data by client
     *
     * @param   string  $client     New connected client
     *
     * @return  array   $message    Outgoing message
     */
    private function filterCacheByClient($client) {
        $messages = [];
        // Timer
        foreach($this->timer as $name => $timer) {
            $duration = (($timer['start'] + $timer['duration']) - time());
            if($duration > 0)
                $messages[] = $this->createMessage('timer', $name, $duration);
        }
        // List
        foreach($this->cache['list'] as $name => $list) {
            foreach($list as &$row) {
                $message = $this->filterRow($client, 'list', $name, $row);
                if($message)
                    $messages[] = $message;
            }
        }
        // Dict
        foreach($this->cache['dict'] as $name => $data) {
            $message = $this->filterRow($client, 'dict', $name, $data);
            if($message)
                $messages[] = $message;
        }
        return $messages;
    }

    /**
     * Filter cache row
     *
     * @param   string  $client     New connected client
     * @param   string  $type       Data type ('list' or 'dict')
     * @param   string  $name       Name of the data
     * @param   data    $data       Data to send to the clients
     *
     * @return  array   $message    Outgoing message
     */
    protected function filterRow($client, $type, $name, $data) {
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
        if(isset($message['data']['auth']))
            $message_auth   = $message['data']['auth'];
        if(isset($message['data']['data']))
            $message['data']    = $message['data']['data'];
        if($config['save_auth']) {
            if($message_auth == $client->login)
                $message['auth'] = true;
            else
                unset($message['auth']);
        }
        return $message;
    }

    /**
     * Send message to all connected clients
     *
     * @param class     $from       From client
     * @param array     $message    Created outgoing message
     * @param array     $config     Config of the message
     */
    private function sendAll($from, $message, $config) {
        if(isset($message['data']['auth']))
            $message_auth   = $message['data']['auth'];
        if(isset($message['data']['data']))
            $message['data']    = $message['data']['data'];
        foreach($this->clients as $client) {
            if(!$client->server) {
                // Check save auth
                if(isset($config['save_auth'])) {
                    if($config['save_auth']) {
                        if($message_auth == $client->login)
                            $message['auth'] = true;
                    }
                }
                // Check Auth
                if(!empty($config['auth'])) {
                    foreach($config['auth'] as $auth => $settings) {
                        if($settings == 'only') {
                            if($client->{$auth} == $from->{$auth})
                                $client->send(json_encode([$message]));
                        } else {
                            if($client->{$auth})
                                $client->send(json_encode([$message]));
                        }
                    }
                } else {
                    $client->send(json_encode([$message]));
                }
            }
        }
    }

    /**
     * Create outgoing message array
     *
     * @param string    $type   Data type ('list' or 'dict')
     * @param string    $name   Name of the data
     * @param data      $data   Data to send to the clients
     */
    private function createMessage($type, $name, $data) {
        return [
            'type' => ($type == 'dict' OR $type == 'list') ? 'data' : $type,
            'name' => $name,
            'data' => $data
        ];
    }
}
