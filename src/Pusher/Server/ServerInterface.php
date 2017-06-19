<?php
namespace PhpPusher\Server;

use PhpPusher\Helper\Console;
use PhpPusher\Auth\AuthenticationHandler;
use PhpPusher\Config\ConfigHandler;
use PhpPusher\Cache\CacheHandler;
use PhpPusher\Cache\CacheSpecials;
use PhpPusher\Cache\ClientHandler;
use PhpPusher\Cache\FilterHandler;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ServerInterface implements MessageComponentInterface
{
    use CacheHandler;
    use CacheSpecials;
    use ClientHandler;
    use FilterHandler;
    use ConfigHandler;
    use MessageHandler;
    use AuthenticationHandler;

    /**
     * Server Authentication Key
     */
    private $key;

    /**
     * Make local Variables
     */
    public function __construct($key, $config) {
        $this->key      = $key;
        $this->setConfig($config);
        $this->clients  = new \SplObjectStorage;
    }

    public function addIoServer($io_server) {
        $this->io_server = $io_server;
    }

    /**
     * Check Authentication for new connection
     * Send Cached data.
     * @param $conn => connection
     */
    public function onOpen(ConnectionInterface $client) {
        // Check Authentication of the Connection
        $client = $this->authenticate($client);
        // Store the new connection to send messages to later
        $this->clients->attach($client);
        //$this->onlineCounter();
        // Send Cached Data to client
        $this->sendEverything($client);
        // Console output
        Console::line('[#'.$client->resourceId.']: Connected. Server:('.$client->server.') Admin:('.$client->admin.') Login:('.$client->login.') Url:('.$client->url.')');
    }

    /**
     * ...
     * @param $from => Connection of sender
     * @param $msg  => Message
     */
    public function onMessage(ConnectionInterface $from, $message) {
        // Console Output
        Console::line('[#'.$from->resourceId.']: Message '.$message);
        // Filter Message
        $this->newMessage($from, $message);
        //$filtered_msg = $this->filter->message($message);
        // Check for Success
        //if(!$filtered_msg['success'])
        //    return false;
        //$this->cache->push($from, $filtered_msg['category'], $filtered_msg['data'], $filtered_msg['event']);
    }

    /**
     * Remove Connection from $this->clients
     * @param $conn => Connection
     */
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        // Console Output
        Console::line('[#'.$conn->resourceId.']: Disconnected.');
    }

    /**
     * Close connection on Error
     * @param $conn => Connection
     * @param $e    => Error
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        Console::line('[#'.$conn->resourceId.']: Error: '.$e->getMessage());
        $conn->close();
    }
}
