<?php
use PhpPusher\Server;

require_once __DIR__ . "/../../vendor/autoload.php";

$config = require('config.php');
$server = new Server('PASSWORT123', $config, 8080);
$server->run();
