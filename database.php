<?php

$host = 'localhost';
$user = 'root';
$password = '';
$dbName = 'cy_cinemas';

$conn = new mysqli($host, $user, $password, $dbname);
$conn->query('set names utf8mb4');

if ($conn->connect_error) {
    die("Connect failed : {$conn->connect_error}");
}
