<?php
namespace PhpPusher\Server;

use PhpPusher\Helper\Console;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\Websocket\WsServer;

abstract class RatchetIoServer
{

    /**
     * Server Port
     */
    protected $port = 8080;

    /**
     * Server Key
     */
    protected $key = 'Password';

    public function setConfig(array $config) {
        // Set Server
        $interface          = new ServerInterface($this->key, $config);
        $ws_server          = new WsServer($interface);
        $http_server        = new HttpServer($ws_server);
        $this->io_server    = IoServer::factory($http_server, $this->port);
        // Add Io Server to Interface
        $interface->addIoServer($this);
    }

    /**
     * Run the Websocket server.
     */
    public function run() {
        if(!isset($this->io_server)) {
            Console::line('You need to set the config first!');
            return false;
        }
        $this->io_server->run();
    }

}
