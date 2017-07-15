<?php

namespace PhpPusher\Message;

/**
 * Handle incomming messages.
 */
trait IncommingMessageHandler
{
    /**
     * @var array $require_message_keys Required incomming message keys
     */
    private $require_message_keys = ['type', 'name', 'data'];

    /**
     * Handles incomming Message
     *
     * @param class     $client     The client that has send the message
     * @param string    $message    Incomming message string
     */
    protected function newIncommingMessage($client, $message) {
        // Check if Client is authenticatet as Server
        if(!$client->server)
            return false;
        // Try to decode Message
        $message = $this->decode($message);
        if(!$message)
            return false;
        // Check if Message strucuture is correct
        if(!$this->checkMessageStructure($message))
            return false;
        // Set receiver
        if(isset($message['receiver']))
            $client->login = $message['receiver'];
        // Handle Message
        $this->handleMessage($client, $message);
    }

    /**
     * Handle the incomming message
     *
     * @param class     $client     The client that has send the message
     * @param string    $message    Incomming message string
     */
    private function handleMessage($client, $message) {
        $type   = $this->getTypeFromMessage($message);
        if(!$type)
            return false;
        // Handle 'timer'
        if($type == 'timer') {
            $this->startTimer($client, $message['name'], $message['data']);
            return false;
        }
        // Handle 'list' and 'dict'
        $config = $this->config[$type][$message['name']];
        if($type == 'list')
            $this->handleNewData($client, $type, $message, $config);
        if($type == 'dict')
            $this->handleNewData($client, $type, $message, $config);
    }

    /**
     * Save new Data to Cache if needed,
     * and send new Data to all clients.
     *
     * @param class     $client     The client that has send the message
     * @param string    $type       The type of the incomming message
     * @param string    $message    Incomming message string
     * @param array     $config     Config of the incomming message type
     */
    private function handleNewData($client, $type, $message, $config) {
        // Check authentication
        if(!empty($config['auth'])) {
            if($config['auth']['login'] === true AND $config['save_auth'])
                // Save authentication id to data
                $message['data']['auth'] = $client->login;
        }
        // Check if data need to be saved to cache
        if($config['cache']) {
            $message['cache'] = [
                'data' => $message['data'],
                'auth' => ($config['save_auth']) ? $client->login : null
            ];
            if($type == 'list') {
                if(!isset($this->cache['list'][$message['name']]) OR !$message['data'])
                    $this->cache['list'][$message['name']] = [];
                if($message['data'])
                    $this->cache['list'][$message['name']][] = $message['cache'];
            } else if($type == 'dict') {
                $this->cache['dict'][$message['name']] = $message['cache'];
            }
        } else {
            $message['cache'] = $message['data'];
        }
        // Send
        $this->sendNewDataToAll($client, $type, $message['name'], $message['cache']);
    }

    /**
     * Get type from message.
     *
     * @param   string  $message    Message
     * @return  string  $type       The type containing to the message, false if there is no matching type
     */
    protected function getTypeFromMessage($message) {
        // Check if type is Timer
        if($message['type'] == 'timer')
            return 'timer';
        // Check if type is List
        if(array_key_exists($message['name'], $this->config['list']))
            return 'list';
        // Check if type is Dictionary
        if(array_key_exists($message['name'], $this->config['dict']))
            return 'dict';
        return false;
    }

    /**
     * Check if message (array) has required keys.
     *
     * @param   array   $message    Incomming message
     * @return  bool
     */
    private function checkMessageStructure($message) {
        if(count(array_intersect_key(array_flip($this->require_message_keys), $message)) !== count($this->require_message_keys))
            return false;
        return true;
    }

    /**
     * Try to Json decode message
     *
     * @param   string $string  String that should be decoded
     * @return  array  $array   Decoded {$string}, false if decoding failed
     */
    private function decode($string) {
        $array = @json_decode($string, true);
        if($array === null)
            return false;
        return $array;
    }
}
