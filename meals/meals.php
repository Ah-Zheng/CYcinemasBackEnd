<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        if ($url == '') {
            addMeals();
        } else {
            updateMeals($url['']);
        }
        break;
    case 'GET':
        if (isset($url[0]) && $url[0] == 'category') {
            getCategory();
            exit();
        }
        getMealsData($url[0]);
        break;
    case 'DELETE':
        deleteMeals($url[0]);
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 取得餐點資料
function getMealsData($mealsId = '')
{
    global $conn;
    if ($mealsId == '') {
        $sql = 'SELECT * FROM `meals`';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } else {
        $mealsId = intval($mealsId);
        $sql = 'SELECT * FROM `meals` WHERE `id` = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $newsId);
        $res->execute();
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// 取得分類
function getCategory()
{
    global $conn;
    $sql = 'SELECT DISTINCT `category` FROM `meals`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}
