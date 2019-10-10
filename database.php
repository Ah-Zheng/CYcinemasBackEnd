<?php

$host = 'ah-zheng.com';
$user = 'ahzheng_cy_cinemas';
$password = 'cy_cinemas';
$dbName = 'ahzheng_cy_cinemas';

// $host = 'localhost';
// $user = 'root';
// $password = '';
// $dbName = 'cy_cinemas';

try {
    $conn = new PDO("mysql:host={$host}; dbname={$dbName}; charset=utf8", $user, $password);
} catch (PDOException $e) {
    $data = [
        'msg' => $e->getMessage(),
    ];
    echo json_encode($data);
}
