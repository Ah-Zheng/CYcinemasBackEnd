<?php

$host = 'localhost';
$user = 'root';
$password = '';
$dbName = 'cy_cinemas';

try {
    $conn = new mysqli($host, $user, $password, $dbName);
    $conn->query('set names utf8mb4');
} catch (Exception $e) {
    $data = [
        'msg' => $e->errorMessage(),
    ];
    echo json_encode();
}
