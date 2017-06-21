<?php
namespace PhpPusher\Server;

// Console logger
use PhpPusher\Helper\Console;

// Authentication handler
use PhpPusher\Auth\AuthenticationHandler;

// Configuration handler
use PhpPusher\Config\ConfigHandler;

// Cache handler
use PhpPusher\Cache\CacheSpecials;
use PhpPusher\Cache\CacheVariables;

// Message handler
use PhpPusher\Message\OutgoingMessageHandler;
use PhpPusher\Message\IncommingMessageHandler;

// Ratchet imports
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ServerInterface implements MessageComponentInterface
{
    use CacheVariables;
    use CacheSpecials;

    use ConfigHandler;
    use AuthenticationHandler;

    use OutgoingMessageHandler;
    use IncommingMessageHandler;

    /**
     * Server Authentication Key
     */
    private $key;

    /**
     * Make local Variables
     *
     * @param string    $key        Key that is used to authenticate allowed clients, that can push data
     * @param array     $config     Configuration array
     */
    public function __construct($key, array $config) {
        $this->key      = $key;
        $this->setConfig($config);
        $this->clients  = new \SplObjectStorage;
    }

    public function addIoServer($ratchet) {
        $this->ratchet = $ratchet;
    }

    /**
     * Handle new connection.
     *
     * @param class     $client     New client that has connected
     */
    public function onOpen(ConnectionInterface $client) {
        // Check Authentication of the Connection
        $client = $this->authenticate($client);
        // Store the new connection to send messages to later
        $this->clients->attach($client);
        // Update online Counter
        $this->onlineCounter($client);
        // Send Cached Data to client
        $this->sendAllDataToNewClient($client);
        // Console output
        Console::line('[#'.$client->resourceId.']: Connected. Server:('.$client->server.') Admin:('.$client->admin.') Login:('.$client->login.') Url:('.$client->url.')');
    }

    /**
     * Handle incomming message.
     *
     * @param class     $from   Connection of sender
     * @param string    $msg    Incomming message string
     */
    public function onMessage(ConnectionInterface $from, $message) {
        // Console Output
        Console::line('[#' . $from->resourceId . ']: Message ' . $message . '.');
        // Filter Message
        $this->newIncommingMessage($from, $message);
    }

    /**
     * Handle closed connection.
     *
     * @param class     $client     Client that has closed the connection
     */
    public function onClose(ConnectionInterface $client) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($client);
        // Console Output
        Console::line('[#'.$client->resourceId.']: Disconnected.');
    }

    /**
     * Handle errors.
     *
     * @param class     $client     Client
     * @param error     $e          Error
     */
    public function onError(ConnectionInterface $client, \Exception $e) {
        Console::line('[#'.$client->resourceId.']: Error: '.$e->getMessage());
        $client->close();
    }
}
