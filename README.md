# PHP Pusher

A PHP Websocket framework that is build on [Ratchet](http://socketo.me/) to create a powerfull Websocket Server,
that pushes Data to your Clients to keep them up to Date.

## Requirements

Shell access is required and root access is recommended.

## Code Example

Server:

```php
<?php
use PhpPusher\Server;

require_once __DIR__ . "/vendor/autoload.php";

// config
$config = [
    'list' => [
        'chat_messages' => [
            'cache' => false,
        ]
    ],
    'dict' => [
        'player' => [
            'auth' => ['login' => true]
        ]
    ],
    'specials' => [
        'online_counter' => true
    ]
];
// Server Key
$key = "Password";
// Port
$port = 8080;
// Create Server
$server = new Server($key, $config, $port);
$server->run();
```

Client:

```php
<?php
use PhpPusher\Client;

require_once __DIR__ . "/vendor/autoload.php";

$client = new Client('Password', '', 'localhost', 8080);

echo $client->send('chat_message', 'Hi');
echo $client->send('player', ['name' => 'Player1', 'data' => 'Some Data.']);
// Start a Timer
$name       = 'game';   // Timer name
$duration   = 30;       // Timer duration in seconds
$wait       = true;     // Wait for the {$duraiton} seconds or continue now
echo $client->startTimer($name, $duration, $wait);
```
