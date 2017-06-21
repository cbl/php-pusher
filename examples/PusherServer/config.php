<?php
return [

    /**
     * List
     *
     * Options:
     * @param (boolean) cache
     * - Default:       true
     * - Description:   Should the data be saved to Cache.
     * @param (string)  send_to
     * - Default:       'all'
     * - Description:   Send Data only to clients with the url {send_to} (Send to all clients if value is 'all')
     * @param (list)    auth
     * - Default:       []
     * - Example:       ['login'=>'only', 'admin'=>false]
     * - Description:   Send Data only to authenticatet Clients (Send to all clients if list is empty) (if is set to true, only send to auth with matching id)
     * @param (boolean) save_auth
     * - Default:       false
     * - Description:   Save the user Id of the referring user, and send {data: $data, auth: true}
     */
    'list' => [
        'chat_message' => [
            'save_auth' => true
        ],
        'wallet' => [
            'cache' => false,
            'auth'  => ['login' => 'only']
        ]
    ],

    /**
     * Dictonary
     *
     * Options:
     * @param (boolean) cache
     * - Default:       true
     * - Description:   Should the data be saved to Cache.
     * @param (string)  send_to
     * - Default:       'all'
     * - Description:   Send Data only to clients with the url {send_to} (Send to all clients if value is 'all')
     * @param (list)    auth
     * - Default:       []
     * - Example:       ['user', 'admin']
     * - Description:   Send Data only to authenticatet Clients (Send to all clients if list is empty)
     * @param (boolean) save_auth
     * - Default:       false
     * - Description:   Save the user Id of the referring user, and send {data: $data, auth: true}
     */
    'dict' => [
        'game' => [
            
        ]
    ],

    /**
     * Specials
     *
     * Options:
     * - 'name of special' => true / (max pool time duration) # Update every change if is set to true
     */
    'specials' => [
        'online_counter' => 0.5,
    ],

    /**
     * Server
     *
     * Options:
     * - 'name' => pool time duration
     */
    'server' => [
        'cpu_usage'     => 0.5,
        'cache_size'    => true,
    ]
];
