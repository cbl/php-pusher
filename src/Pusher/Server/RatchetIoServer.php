<?php
namespace PhpPusher\Server;

use PhpPusher\Helper\Console;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\Websocket\WsServer;

abstract class RatchetIoServer
{

    protected static $DEFAULT_PORT = 8080;

    /**
     * IoServer Instance.
     */
    protected $io_server;

    /**
     * Server Port
     */
    protected $port;

    public function __construct($key, $config, $port = null) {
        // Set Port
        $this->port         = (!$port) ? self::$DEFAULT_PORT : $port;
        // Set Server
        $interface           = new ServerInterface($key, $config);
        $ws_server          = new WsServer($interface);
        $http_server        = new HttpServer($ws_server);
        $this->io_server    = IoServer::factory($http_server, $this->port);
        // Add Io Server to Interface
        $interface->addIoServer($this->io_server);
    }

    public function run() {
        $this->io_server->run();
    }
}
