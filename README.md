# PHP Pusher

A PHP Websocket framework that is build on [Ratchet](http://socketo.me/) to create a powerfull Websocket Server,
that pushes Data to your Clients to keep them up to Date.

## Requirements

Shell access is required and root access is recommended.

## Getting Started

To install the framework, run: ```composer require cbl/php-pusher```

## Code Example

Server:

```php
<?php
use PhpPusher\Server;

require_once __DIR__ . "/../../vendor/autoload.php";

class PusherServer extends Server
{
    public function authLogin($client) {
        $cookies = $client->WebSocket->request->getCookies();
        // return false if the client has no session
        if(!isset($cookies['session']))
            return false;
        $session = $cookies['session'];
        if(!$session)
            return false;
        // Return the user id
        return 5;
        //return getUserIdBySession(urldecode($session));
    }

    public function authAdmin($client) {
        $admin_ids = [1,5,9];
        if(in_array($client->login, $admin_ids))
            return true;
        return false;
    }
}

// config
$config = [
    'list' => [
        'chat_messages' => [
            'save_auth' => true
        ],
        'wallet' => [
            'cache' => false,
            'auth'  => ['login' => 'only']
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
$server = new PusherServer($key, $config, 8080);
$server->run();
```

Client:

```php
<?php
use PhpPusher\Client;

require_once __DIR__ . "/../../vendor/autoload.php";

$key    = 'Password';
$client = new Client($key);

// Set a receiver id
$receiver = 5;
// Send a Chat Message
$client->send('chat_message', 'Hi!', $receiver);
// Send wallet amount only to the receiver
$client->send('wallet', 100, $receiver);
// Start a timer
$client->startTimer('game_timer', 30, true);
// Publish game after timer
$client->send('game', [
    'data' => 'Some Data.'
]);
```
