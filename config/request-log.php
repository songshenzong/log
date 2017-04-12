<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Env Settings
    |--------------------------------------------------------------------------
    |
    |
    */

    'env' => [
        'dev',
        'local',
        'production',
    ],


    /*
    |--------------------------------------------------------------------------
    | Token Settings
    |--------------------------------------------------------------------------
    |
    |
    */

    'token' => [
        'lenovo',
        'shuaijiang',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix Settings
    |--------------------------------------------------------------------------
    |
    |
    */

    'route_prefix' => 'request_logs',


    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    |
    */

    'table' => 'request_logs',


    /*
     |--------------------------------------------------------------------------
     | DataCollectors
     |--------------------------------------------------------------------------
     |
     | Enable/disable DataCollectors
     |
     */

    'collectors' => [
        'phpinfo'    => true,  // Php version
        'messages'   => true,  // Messages
        'time'       => true,  // Time Datalogger
        'memory'     => true,  // Memory usage
        'exceptions' => true,  // Exception displayer
        'log'        => true,  // Logs from Monolog (merged in messages if enabled)
        'db'         => true,  // Show database (PDO) queries and bindings
        'views'      => false,  // Views with their data
        'route'      => true,  // Current route information
        'laravel'    => false, // Laravel version and environment
        'events'     => false, // All events fired
        'request'    => true,  // Only one can be enabled
        'mail'       => true,  // Catch mail messages
        'logs'       => false, // Add the latest log messages
        'files'      => false, // Show the included files
        'config'     => false, // Display config settings
        'auth'       => false, // Display Laravel authentication status
        'gate'       => false, // Display Laravel Gate checks
        'session'    => true,  // Display session data
    ],

    /*
     |--------------------------------------------------------------------------
     | Extra options
     |--------------------------------------------------------------------------
     |
     | Configure some DataCollectors
     |
     */

    'options' => [
        'auth'  => [
            'show_name' => true,   // Also show the users name/email in the Songshenzong
        ],
        'db'    => [
            'with_params' => true,   // Render SQL with the parameters substituted
            'backtrace'   => true,   // Use a backtrace to find the origin of the query in your files.
            'timeline'    => true,  // Add the queries to the timeline
            'explain'     => [                 // Show EXPLAIN output on queries
                                               'enabled' => true,
                                               'types'   => ['SELECT'],     // ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
            ],
            'hints'       => true,    // Show hints for common mistakes
        ],
        'mail'  => [
            'full_log' => true,
        ],
        'views' => [
            'data' => true,    //Note: Can slow down the application, because the data can be quite large..
        ],
        'route' => [
            'label' => true  // show complete route on bar
        ],
        'logs'  => [
            'file' => true,
        ],
    ],


];