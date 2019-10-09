<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'GET':
        getMovies();
        break;
    case 'POST':
        break;
    case 'DELETE':
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 取得所有電影資料
function getMovies()
{
    global $conn;
    $sql = 'SELECT * FROM `movies`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

// 取得上映中電影資料
function getReleasedMovies()
{
}

// 取得即將上映電影資料
function getComingSoonMovies()
{
}
