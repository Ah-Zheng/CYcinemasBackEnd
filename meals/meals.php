<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        if (isset($url[0]) && $url[0] != '') {
            updateMeals($url[0]);
            exit();
        }
        addMeals();
        break;
    case 'GET':
        getMealsData();
        break;
    case 'DELETE':
        deleteMeals($url[0]);
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 取得餐點資料
function getMealsData()
{
    global $conn;
    $sql = 'SELECT * FROM `meals`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

// 新增餐點
function addMeals()
{
    global $conn;
    $name = isset($_POST['name']) ? myFilter($_POST['name']) : '';
    $size = isset($_POST['size']) ? myFilter($_POST['size']) : '';
    $category = isset($_POST['category']) ? myFilter($_POST['category']) : '';
    $price = isset($_POST['price']) ? myFilter($_POST['price']) : '';

    // 判斷是否有上傳
    if (isset($_FILES['file']) && $_FILES['file'] != '') {
        // 判斷檔案大小是否大於 4MB
        if ($_FILES['file']['size'] >= 4 * MB) {
            $data = returnData(413, '上傳圖片不得大於 4MB', STATUS_413);
            echo json_encode($data);
            exit();
        }

        // 判斷是否為圖片 png、jpg
        $allowTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG];
        $detectedType = exif_imagetype($_FILES['file']['tmp_name']);
        $error = !in_array($detectedType, $allowTypes);
        if ($error) {
            $data = returnData(500, '請上傳副檔名為 png 以及 jpg 的圖片', STATUS_500);
            echo json_encode($data);
            exit();
        } else {
            $date = date('Ymdhis');
            uploadImg($_FILES['file'], $date, 'meals', 300, 200, 300, 200); // 上傳檔案 (檔案, 修改的檔名, 資料夾名稱)
            $imgUrl = "https://cy-cinemas.ml/uploads/meals/thumbs/{$date}.png";
        }
    } else {
        $imgUrl = 'http://fakeimg.pl/300x200/?text=CY CINEMAS';
    }

    $sql = 'INSERT INTO `meals` (`name`, `size`, `price`, `category`, `img_url`) VALUES (:mealsName, :size, :price, :category, :imgUrl)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':mealsName', $name);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':imgUrl', $imgUrl);
    if ($stmt->execute()) {
        $data = returnData(201, '新增成功');
    } else {
        $data = returnData(500, '新增失敗', STATUS_500);
    }

    echo json_encode($data);
}

// 更新餐點
function updateMeals($mealsId)
{
    global $conn;
    $mealsId = intval($mealsId);
    $name = isset($_POST['name']) ? myFilter($_POST['name']) : '';
    $size = isset($_POST['size']) ? myFilter($_POST['size']) : '';
    $category = isset($_POST['category']) ? myFilter($_POST['category']) : '';
    $price = isset($_POST['price']) ? myFilter($_POST['price']) : '';

    // 判斷是否有上傳
    if (isset($_FILES['file']) && $_FILES['file'] != '') {
        // 判斷檔案大小是否大於 4MB
        if ($_FILES['file']['size'] >= 4 * MB) {
            $data = returnData(413, '上傳圖片不得大於 4MB', STATUS_413);
            echo json_encode($data);
            exit();
        }

        // 判斷是否為圖片 png、jpg
        $allowTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG];
        $detectedType = exif_imagetype($_FILES['file']['tmp_name']);
        $error = !in_array($detectedType, $allowTypes);
        if ($error) {
            $data = returnData(500, '請上傳副檔名為 png 以及 jpg 的圖片', STATUS_500);
            echo json_encode($data);
            exit();
        } else {
            $date = date('Ymdhis');
            $fileName = isset($_POST['fileName']) ? myfilter($_POST['fileName']) : '';
            uploadImg($_FILES['file'], $date, 'meals', 300, 200, 300, 200); // 上傳檔案 (檔案, 修改的檔名, 資料夾名稱)
            deleteImg($fileName, 'meals');
            $imgUrl = "https://cy-cinemas.ml/uploads/meals/thumbs/{$date}.png";
        }

        $sql = 'UPDATE `meals` SET `name` = :mealsName, `size` = :size, `category` = :category, `price` = :price, `img_url` = :imgUrl WHERE `id` = :mealsId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':imgUrl', $imgUrl);
    } else {
        $sql = 'UPDATE `meals` SET `name` = :mealsName, `size` = :size, `category` = :category, `price` = :price WHERE `id` = :mealsId';
        $stmt = $conn->prepare($sql);
    }

    $stmt->bindParam(':mealsName', $name);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':mealsId', $mealsId);
    if ($stmt->execute()) {
        $data = returnData(201, '修改成功');
    } else {
        $data = returnData(500, '修改失敗', STATUS_500);
    }
    echo json_encode($data);
}

// 刪除餐點
function deleteMeals($mealsId)
{
    global $conn;

    // 取得該消息的圖片檔名
    $mealsId = intval($mealsId);
    $getSql = 'SELECT `img_url` FROM `meals` WHERE `id` = :id';
    $res = $conn->prepare($getSql);
    $res->bindParam(':id', $mealsId);
    $res->execute();
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $fileName = substr($row['img_url'], -18);
    deleteImg($fileName, 'meals'); // 刪除伺服器上的圖片

    // 將該筆消息刪除
    $delSql = 'DELETE FROM `meals` WHERE `id` = :id';
    $stmt = $conn->prepare($delSql);
    $stmt->bindParam(':id', $mealsId);
    if ($stmt->execute()) {
        $data = returnData(200, '刪除成功');
    } else {
        $data = returnData(500, '刪除失敗', STATUS_500);
    }

    echo json_encode($data);
}
