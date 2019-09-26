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
    case 'POST del':
        deleteNews();
        break;
    default:
        echo 'NO';
        break;
}

// 取得最新消息的所有資料
function getNewsData()
{
    require_once '../database.php';
    $sql = 'SELECT * FROM `news`';
    $res = $conn->query($sql);
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
    $sql = 'INSERT INTO `news` (`news_title`, `news_content`) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', );
    $data = [
        'msg' => $txt,
    ];
    echo json_encode($data);
}

// 更新消息
function updateNews()
{
}

// 刪除消息
function deleteNews()
{
    require_once '../database.php';
    $newsId = $_POST['newsId'];
    $sql = 'DELETE FROM `news` WHERE `news_id` = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', intval($newsId));
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
