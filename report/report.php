<?php
/*
@ 報表資料
    - 本周總票數
    - 電影類型
    - 訂票時段
    - 餐點銷量 *
    - 票種
*/

require_once '../header.php';
require_once '../function.php';
require_once '../database.php';
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'GET':
        if (isset($url[0]) && $url[0] == 'ticketType') {
        }
        break;
    case 'POST':
        break;
    case 'DELETE':
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}
