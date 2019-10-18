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
    // 沒ID就取全部
    if ($discountId == '') {
        $sql = 'SELECT * FROM `total_price_discount` ORDER BY `start_time`';
        $stmt = $conn->query($sql);
    } else {
        $discountId = intval($discountId);
        $sql = 'SELECT * FROM `total_price_discount` WHERE `id` = :id ORDER BY `start_time`';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $discountId);
        $stmt->execute();
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

// 新增折扣
function addDiscount()
{
    global $conn;
    $discount = isset($_POST['discount']) ? myFilter($_POST['discount']) : '';
    $description = isset($_POST['description']) ? myFilter($_POST['description']) : '';
    $startTime = isset($_POST['startTime']) ? myFilter($_POST['startTime']) : '';
    $endTime = isset($_POST['endTime']) ? myFilter($_POST['endTime']) : '';

    $sql = 'INSERT INTO `total_price_discount` (`discount`, `description`, `start_time`, `end_time`) VALUES (:discount, :description, :startTime, :endTime)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':discount', $discount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':startTime', $startTime);
    $stmt->bindParam(':endTime', $endTime);
    if ($stmt->execute()) {
        $data = returnData(201, '新增成功');
    } else {
        $data = returnData(500, '新增失敗', STATUS_500);
    }
    echo json_encode($data);
}

// 更新折扣
function updateDiscount($discountId)
{
    global $conn;
    $discountId = intval($discountId);
    $discount = isset($_POST['discount']) ? myFilter($_POST['discount']) : '';
    $description = isset($_POST['description']) ? myFilter($_POST['description']) : '';
    $startTime = isset($_POST['startTime']) ? myFilter($_POST['startTime']) : '';
    $endTime = isset($_POST['endTime']) ? myFilter($_POST['endTime']) : '';

    $sql = 'UPDATE `total_price_discount` SET `discount` = :discount, `description` = :description, `start_time` = :startTime, `end_time` = :endTime WHERE `id` = :id';
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':discount', $discount);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':startTime', $startTime);
    $stmt->bindParam(':endTime', $endTime);
    $stmt->bindParam(':id', $discountId);

    if ($stmt->execute()) {
        $data = returnData(201, '修改成功');
    } else {
        $data = returnData(500, '修改失敗', STATUS_500);
    }
    echo json_encode($data);

}

// 刪除折扣
function deleteDiscount($discountId)
{
    global $conn;

    $delSql = 'DELETE FROM `total_price_discount` WHERE `id` = :id';
    $stmt = $conn->prepare($delSql);
    $stmt->bindParam(':id', $discountId);
    if ($stmt->execute()) {
        $data = returnData(200, '刪除成功');
    } else {
        $data = returnData(500, '刪除失敗', STATUS_500);
    }
    echo json_encode($data);
}
