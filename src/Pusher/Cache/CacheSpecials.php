<?php
namespace PhpPusher\Cache;

use PhpPusher\Helper\Console;

use React\EventLoop\Factory;

trait CacheSpecials
{
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
        $this->sendAll('timer', $name, $duration);
        $this->io_server->loop->addTimer($duration, function() use($client, $name, $duration) {
            $this->sendAll('timer_end', $name, 0);
            Console::line("[#{$client->resourceId}][Timer][{$name}] Timer end. ({$duration})");
            unset($this->timer[$name]);
        });
    }
}
