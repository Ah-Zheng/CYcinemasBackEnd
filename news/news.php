<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        if ($url[0] == '') {
            addNews();
        } else {
            updateNews($url[0]);
        }
        break;
    case 'GET':
        getNewsData($url[0]);
        break;
    case 'DELETE':
        deleteNews($url[0]);
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 取得最新消息的所有資料
function getNewsData($newsId = '')
{
    global $conn;

    // 透過有無 newsId 來決定是撈出所有資料還是單筆資料
    if ($newsId == '') {
        $sql = 'SELECT * FROM `news` ORDER BY `release_time` DESC';
        $stmt = $conn->query($sql);
    } else {
        $newsId = intval($newsId);
        $sql = 'SELECT * FROM `news` WHERE `id` = :id ORDER BY `release_time` DESC';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $newsId);
        $stmt->execute();
    }
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

// 新增消息
function addNews()
{
    global $conn;
    $title = isset($_POST['title']) ? myFilter($_POST['title']) : '';
    $content = isset($_POST['content']) ? myFilter($_POST['content']) : '';
    $startTime = isset($_POST['startTime']) ? myFilter($_POST['startTime']) : '';
    $endTime = isset($_POST['endTime']) ? myFilter($_POST['endTime']) : '';

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
            uploadImg($_FILES['file'], $date, 'news'); // 上傳檔案 (檔案, 修改的檔名, 資料夾名稱)
            $normalUrl = "https://cy-cinemas.ml/uploads/news/normal/{$date}.png";
            $thumbsUrl = "https://cy-cinemas.ml/uploads/news/thumbs/{$date}.png";
        }
    } else {
        $normalUrl = 'http://fakeimg.pl/300x400/?text=CY CINEMAS';
        $thumbsUrl = 'http://fakeimg.pl/300x400/?text=CY CINEMAS';
    }

    $sql = 'INSERT INTO `news` (`title`, `content`, `img_normal_url`, `img_thumbs_url`, `start_time`, `end_time`) VALUES (:title, :content, :normalUrl, :thumbsUrl, :startTime, :endTime)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':normalUrl', $normalUrl);
    $stmt->bindParam(':thumbsUrl', $thumbsUrl);
    $stmt->bindParam(':startTime', $startTime);
    $stmt->bindParam(':endTime', $endTime);
    if ($stmt->execute()) {
        $data = returnData(201, '新增成功');
    } else {
        $data = returnData(500, '新增失敗', STATUS_500);
    }
    echo json_encode($data);
}

// 更新消息
function updateNews($newsId)
{
    global $conn;
    $newsId = intval($newsId);
    $title = isset($_POST['title']) ? myFilter($_POST['title']) : '';
    $content = isset($_POST['content']) ? myFilter($_POST['content']) : '';
    $startTime = isset($_POST['startTime']) ? myFilter($_POST['startTime']) : '';
    $endTime = isset($_POST['endTime']) ? myFilter($_POST['endTime']) : '';

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
            uploadImg($_FILES['file'], $date, 'news'); // 上傳檔案 (檔案, 修改的檔名, 資料夾名稱)
            deleteImg($fileName, 'news');
            $normalUrl = "https://cy-cinemas.ml/uploads/news/normal/{$date}.png";
            $thumbsUrl = "https://cy-cinemas.ml/uploads/news/thumbs/{$date}.png";
        }

        $sql = 'UPDATE `news` SET `title` = :title, `content` = :content, `start_time` = :startTime, `end_time` = :endTime, `img_normal_url` = :normalUrl, `img_thumbs_url` = :thumbsUrl WHERE `id` = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':normalUrl', $normalUrl);
        $stmt->bindParam(':thumbsUrl', $thumbsUrl);
    } else {
        $sql = 'UPDATE `news` SET `title` = :title, `content` = :content, `start_time` = :startTime, `end_time` = :endTime WHERE `id` = :id';
        $stmt = $conn->prepare($sql);
    }

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':startTime', $startTime);
    $stmt->bindParam(':endTime', $endTime);
    $stmt->bindParam(':id', $newsId);
    if ($stmt->execute()) {
        $data = returnData(200, '修改成功');
    } else {
        $data = returnData(500, '修改失敗', STATUS_500);
    }
    echo json_encode($data);
}

// 刪除消息
function deleteNews($newsId)
{
    global $conn;

    // 取得該消息的圖片檔名
    $newsId = intval($newsId);
    $getSql = 'SELECT `img_normal_url` FROM `news` WHERE `id` = :id';
    $res = $conn->prepare($getSql);
    $res->bindParam(':id', $newsId);
    $res->execute();
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $fileName = substr($row['img_normal_url'], -18);
    deleteImg($fileName, 'news'); // 刪除伺服器上的圖片

    // 將該筆消息刪除
    $delSql = 'DELETE FROM `news` WHERE `id` = :id';
    $stmt = $conn->prepare($delSql);
    $stmt->bindParam(':id', $newsId);
    if ($stmt->execute()) {
        $data = returnData(200, '刪除成功');
    } else {
        $data = returnData(500, '刪除失敗', STATUS_500);
    }
    echo json_encode($data);
}
