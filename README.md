#PHP Pusher

A PHP Websocket framework that is build on [Ratchet](http://socketo.me/) to create a powerfull Websocket Server,
that pushes Data to your Clients to keep them up to Date.

##Requirements

Shell access is required and root access is recommended.

##Code Example

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
            'some_data' => [
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
