<?php

$db = [
    'host' => 'localhost',
    'database' => 'biadms_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
return new PDO(
    "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}",
    $db['username'],
    $db['password'],
    $db['options']

);