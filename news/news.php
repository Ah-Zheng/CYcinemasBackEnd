<?php

require_once '../header.php';
require_once '../function.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method.' '.$url[0]) {
    case 'POST news':
        break;
    case 'GET news':
        getNewsData();
        break;
    default:
        echo 'NO';
        break;
}

// 取得最新消息的所有資料
function getNewsData()
{
    $txt = $_POST['txt'];
    $data = [
        'msg' => $txt,
    ];
    echo json_encode($data);
}
