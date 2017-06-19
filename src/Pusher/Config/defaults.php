<?php
return [

    /**
     * List
     *
     * Options:
     * @param (boolean) cache       Should the data be saved to Cache.
     * @param (string)  send_to     Send Data only to clients with the url {send_to} (Send to all clients if value is 'all')
     * @param (list)    auth        Send Data only to authenticatet Clients (Send to all clients if list is empty)
     * @param (boolean) save_auth   Save the user Id of the referring user, and send {data: $data, auth: true}
     */
    'list' => [
        'cache'     => true,
        'send_to'   => 'all',
        'auth'      => [],
        'save_auth' => false
    ],

    /**
     * Dictonary
     *
     * Options:
     * @param (boolean) cache       Should the data be saved to Cache.
     * @param (string)  send_to     Send Data only to clients with the url {send_to} (Send to all clients if value is 'all')
     * @param (list)    auth        Send Data only to authenticatet Clients (Send to all clients if list is empty)
     * @param (boolean) save_auth   Save the user Id of the referring user, and send {data: $data, auth: true}
     */
    'dict' => [
        'cache'     => true,
        'send_to'   => 'all',
        'auth'      => [],
        'save_auth' => false
    ],

    /**
     * Timer
     *
     * Options:
     * @param (integer) duration    Duration of the timer in seconds
     * @param (string)  send_to     Send Data only to clients with the url {send_to} (Send to all clients if value is 'all')
     * @param (list)    auth Send   Data only to authenticatet Clients (Send to all clients if list is empty)
     */
    'timer' => [
        'duration'  => 30,
        'send_to'   => 'all',
        'auth'      => []
    ]
];
