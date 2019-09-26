<?php

$host = 'ah-zheng.com';
$user = 'ahzheng_cy_cinemas';
$password = 'cy_cinemas';
$dbName = 'ahzheng_cy_cinemas';

try {
    $conn = new mysqli($host, $user, $password, $dbName);
    $conn->query('set names utf8mb4');
} catch (Exception $e) {
    $data = [
        'msg' => $e->errorMessage(),
    ];
    echo json_encode();
}
