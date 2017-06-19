<?php
use PhpPusher\Client;

require_once __DIR__ . "/../../vendor/autoload.php";

$client = new Client('PASSWORT123');

echo $client->send('chat_message', 'Hallo');

echo $client->send('roulette_game', ['id' => 'lolo']);

echo $client->startTimer('roulette', 30);
