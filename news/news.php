<?php

require_once '../header.php';
require_once '../function.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        addNews();
        break;
    case 'GET':
        getNewsData($url[0]);
        break;
    case 'PUT':
        updateNews();
        break;
    case 'DELETE':
        deleteNews($url[0]);
        break;
    default:
        echo 'NO';
        break;
}

// 取得最新消息的所有資料
function getNewsData($newsId = '')
{
    require_once '../database.php';
    $newsId = intval($newsId);
    if ($newsId != '') {
        $sql = "SELECT * FROM `news` WHERE `id` = {$newsId} ORDER BY `release_time` DESC";
    } else {
        $sql = 'SELECT * FROM `news` ORDER BY `release_time` DESC';
    }
    $res = $conn->query($sql);
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    $conn->close();

    echo json_encode($data);
}

// 新增消息
function addNews()
{
    require_once '../database.php';
    $title = $_POST['title'];
    $content = $_POST['content'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $date = date('Ymdhis');
    if (isset($_FILES['file'])) {
        uploadImg($_FILES['file'], $date, 'news'); // 上傳檔案 (檔案, 修改的檔名, 資料夾名稱)
        $normalUrl = "https://cy-cinemas.ml/uploads/news/normal/{$date}.png";
        $thumbsUrl = "https://cy-cinemas.ml/uploads/news/thumbs/{$date}.png";
    } else {
        $normalUrl = '';
        $thumbsUrl = '';
    }

    $sql = 'INSERT INTO `news` (`title`, `content`, `img_normal_url`, `img_thumbs_url`, `start_time`, `end_time`) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', $title, $content, $normalUrl, $thumbsUrl, $startTime, $endTime);
    if ($stmt->execute()) {
        $data = [
            'msg' => '新增成功',
        ];
    } else {
        $data = [
            'msg' => '新增失敗',
        ];
    }
    $conn->close();

    echo json_encode($data);
}

// 更新消息
function updateNews()
{
}

// 刪除消息
function deleteNews($newsId)
{
    require_once '../database.php';
    $newsId = intval($newsId);

    $getSql = "SELECT `img_normal_url` FROM `news` WHERE `id` = {$newsId}";
    $res = $conn->query($getSql);
    $row = $res->fetch_assoc();

    $fileName = substr($row['img_normal_url'], -18);
    deleteImg($fileName, 'news');

    $delSql = 'DELETE FROM `news` WHERE `id` = ?';
    $stmt = $conn->prepare($delSql);
    $stmt->bind_param('i', $newsId);
    if ($stmt->execute()) {
        $data = [
            'msg' => '刪除成功',
        ];
    } else {
        $data = [
            'msg' => '刪除失敗',
        ];
    }
    $conn->close();

    echo json_encode($data);
}
