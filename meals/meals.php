<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        addMeals();
        break;
    case 'GET':
        getMealsData($url[0]);
        break;
    case 'PUT':
        updateMeals();
        break;
    case 'DELETE':
        deleteMeals($url[0]);
        break;
    default:
        returnData(404, '404 NOT FOUND', STATUS_404);
        break;
}

// 取得餐點資料
function getNewsData($mealsId = '')
{
    global $conn;
    if ($mealsId == '') {
        $sql = 'SELECT * FROM `meals`';
        $res = $conn->query($sql);
    } else {
        $mealsId = intval($mealsId);
        $sql = 'SELECT * FROM `meals` WHERE `id` = ?';
        $res = $conn->prepare($sql);
        $res->bind_param('i', $mealsId);
        $res->execute();
    }
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
}

// 新增餐點
function addMeals()
{
}

// 更新餐點
function updateMeals()
{
}

// 刪除餐點
function deleteMeals()
{
}
