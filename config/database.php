<?php
 require '../config/app.php';
 $db = DB;
return new PDO(
    "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}",
    $db['username'],
    $db['password'],
    $db['options']
);