<?php

namespace PhpPusher\Cache;

trait FilterHandler
{
    protected function filterCacheByClient($client) {
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
        if($config['save_auth']) {
            if($message['auth'] == $client->login)
                $message['auth'] = true;
            else
                unset($message['auth']);
        }
        return $message;
    }

    protected function createMessage($type, $name, $data) {
        return [
            'type' => ($type == 'dict' OR $type == 'list') ? 'data' : $type,
            'name' => $name,
            'data' => $data
        ];
    }
}
