<?php
namespace PhpPusher\Cache;

use PhpPusher\Helper\Console;

use React\EventLoop\Factory;

trait CacheSpecials
{
    /**
     * Start a Timer.
     * Send 'timer' to all clients, send 'timer_end' when the timer has ended.
     *
     * @param class     $client     The connected client that has send the 'start_timer' request
     * @param string    $name       The name of the timer
     * @param int       $duration   The duration of the timer
     */
    protected function startTimer($client, $name, $duration) {
        // Check if Timer is already running
        if(isset($this->timer[$name])) {
            Console::line("[Error]: Timer '{$name}' is already running.");
            return false;
        }
        // Save Timer to Cache
        $this->timer[$name] = [
            'start'     => time(),
            'duration'  => $duration
        ];
        // Sleep in Background
        Console::line("[#{$client->resourceId}][Timer][{$name}] Start Timer. ({$duration})");
        $this->sendAll($client, $this->createMessage('timer', $name, $duration), []);
        $this->ratchet->io_server->loop->addTimer($duration, function() use($client, $name, $duration) {
            $this->sendAll($client, $this->createMessage('timer_end', $name, 0), []);
            Console::line("[#{$client->resourceId}][Timer][{$name}] Timer end. ({$duration})");
            unset($this->timer[$name]);
        });
    }

    /**
     * Online Counter.
     * Send the number of connected clients that are logged in, to every client.
     *
     * @param class     $client     New connected client
     */
    protected function onlineCounter($client) {
        if(!$client->login OR $client->server)
            return false;
        // Count the number of logged in clients
        $connected_clients_count = 0;
        foreach($this->clients as $client) {
            if($client->login AND !$client->server)
                $connected_clients_count++;
        }
        // Send it to clients
        $message = $this->createMessage('online_counter', 'online', $connected_clients_count);
        foreach($this->clients as $client) {
            if(!$client->server)
                $client->send(json_encode([$message]));
        }
    }
}
