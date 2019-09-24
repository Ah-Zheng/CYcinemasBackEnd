<?php

require_once '../header.php';
require_once '../function.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method.' '.$url[0]) {
    case 'POST test':
        getNewsData();
        break;
    default:
        echo 'NO';
        break;
}

function getNewsData()
{
    $txt = $_POST['txt'];
    $data = [
        'msg' => $txt,
    ];
    echo json_encode($data);
}
