<?php 
    define('APP_NAME', 'Barangay Information and Aid Distribution Management System');
    define('APP_ENV', 'development'); // change to 'production' in production environment
    define('DB',[
        'host' => 'localhost',
        'database' => 'biadms',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ]);

?>