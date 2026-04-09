<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'db2'),

    'connections' => [

        'db2' => [
            'driver'      => 'db2_ibmi_odbc',
            'host'        => env('DB_HOST', 'localhost'),
            'port'        => env('DB_PORT', '446'),
            'database'    => env('DB_DATABASE', '*LOCAL'),
            'username'    => env('DB_USERNAME', ''),
            'password'    => env('DB_PASSWORD', ''),
            'schema'      => env('DB2_SCHEMA', 'SOLIISS'),
            'driverName'  => env('DB2_ODBC_DRIVER', env('DB2_DRIVER', '{IBM i Access ODBC Driver}')),
            'prefix'      => '',
            'date_format' => 'Y-m-d H:i:s',
            'options'     => [
                PDO::ATTR_CASE       => PDO::CASE_LOWER,
                PDO::ATTR_PERSISTENT => false,
            ],
        ],

    ],

    'migrations' => [
        'table'                  => 'migrations',
        'update_date_on_publish' => true,
    ],

];