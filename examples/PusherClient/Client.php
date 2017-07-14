<?php
use PhpPusher\Client;

require_once __DIR__ . "/../../vendor/autoload.php";

$client = new Client('Password');

$receiver = 5;
// Send a Chat Message
$client->send('chat_message', 'Hi!', $receiver);
// Reset Chat Messages
$client->reset('chat_message');
// Send wallet amount only to the receiver
$client->send('wallet', 100, $receiver);
// Start a timer
$client->startTimer('game_timer', 30, true);
// Publish game after timer
$client->send('game', [
    'data' => 'Some Data.'
]);
