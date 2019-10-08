<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'GET':
            getDiscountData($url[0]);
        break;
    case 'POST':
        if ($url['0'] == '') {
            addDiscount();
        } else {
            updateDiscount($url[0]);
        }
        break;
    case 'DELETE':
        deleteDiscount($url[0]);
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 獲取折扣資料
function getDiscountData($discountId = '')
{
    global $conn;
}

// 新增折扣
function addDiscount()
{
    global $conn;
}

// 更新折扣
function updateDiscount($discountId)
{
    global $conn;
}

// 刪除折扣
function deleteDiscount($discountId)
{
    global $conn;
}
