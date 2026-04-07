<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | SOLI-ISS  -  Configuracion de Base de Datos
    | Driver:  cooperl/laravel-db2
    | Servidor: IBM iSeries Power9  (misma configuracion que ClubISS)
    | Schema:  SOLIISS
    |--------------------------------------------------------------------------
    */

    'default' => env('DB_CONNECTION', 'db2'),

    'connections' => [

        // ── Conexion principal: IBM DB2 for i (iSeries Power9) ──────────────
        'db2' => [
            'driver'      => 'db2_ibmi_odbc',
            'host'        => env('DB_HOST', 'localhost'),
            'port'        => env('DB_PORT', '446'),
            'database'    => env('DB_DATABASE', '*LOCAL'),
            'username'    => env('DB_USERNAME', ''),
            'password'    => env('DB_PASSWORD', ''),
            'schema'      => env('DB2_SCHEMA', 'SOLIISS'),
            'driverName'  => env('DB2_DRIVER', '{iSeries Access ODBC Driver}'),
            'prefix'      => '',
            'date_format' => 'Y-m-d H:i:s',
            'options'     => [
                PDO::ATTR_CASE       => PDO::CASE_LOWER,
                PDO::ATTR_PERSISTENT => false,
            ],
        ],

        // ── SQLite (solo para tests unitarios locales) ──────────────────────
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE_SQLITE', database_path('soliiss_test.sqlite')),
            'prefix'   => '',
        ],

        // ── MySQL (reservado para futuras integraciones) ────────────────────
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('MYSQL_HOST', '127.0.0.1'),
            'port'      => env('MYSQL_PORT', '3306'),
            'database'  => env('MYSQL_DATABASE', ''),
            'username'  => env('MYSQL_USERNAME', ''),
            'password'  => env('MYSQL_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    | Tabla donde Laravel registra las migraciones ejecutadas.
    | Se crea automaticamente en el schema SOLIISS.
    */
    'migrations' => [
        'table'       => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis (deshabilitado en v1.0)
    |--------------------------------------------------------------------------
    */
    'redis' => [
        'client'  => env('REDIS_CLIENT', 'phpredis'),
        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],

];
