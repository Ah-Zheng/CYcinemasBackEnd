<?php

require_once '../header.php';
require_once '../function.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method.' '.$url[0]) {
    case 'POST add':
        addNews();
        break;
    case 'GET get':
        getNewsData();
        break;
    case 'POST upd':
        updateNews();
        break;
    case 'DELETE del':
        deleteNews($url[1]);
        break;
    default:
        echo 'NO';
        break;
}

// 取得最新消息的所有資料
function getNewsData()
{
    require_once '../database.php';
    $sql = 'SELECT * FROM `news` ORDER BY `news_time` ASC';
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
    $fileName = $_FILES['file']['name'];

    $na = explode('.', rtrim($fileName, '.'));
    uploadImg($_FILES['file'], $na[0]);
    $normalUrl = "https://cy-cinemas.ml/uploads/news/normal/{$na[0]}.png";
    $thumbsUrl = "https://cy-cinemas.ml/uploads/news/thumbs/{$na[0]}.png";

    $sql = 'INSERT INTO `news` (`news_title`, `news_content`, `news_img_normal_url`, `news_img_thumbs_url`)
            VALUES (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $title, $content, $normalUrl, $thumbsUrl);
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
    $sql = 'DELETE FROM `news` WHERE `news_id` = ?';
    $stmt = $conn->prepare($sql);
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
