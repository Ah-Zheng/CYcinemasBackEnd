<?php

require_once '../header.php';
require_once '../function.php';
require_once '../config.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'GET':
        if (isset($url[0]) && $url[0] == 'showMovies') {
            getTypeMovies($url[1], intval($url[2])); // ('[ released | comingSoon ]', [ 0 | 1 ])
            exit();
        } elseif (isset($url[0]) && $url[0] == 'addShowMovies') {
            setShowMovies($url[1], 1); // (movieId, 1)
            exit();
        } elseif (isset($url[0]) && $url[0] == 'removeShowMovies') {
            setShowMovies($url[1], 0); // (movieId, 0)
            exit();
        } 
        getMovies();
        break;
    case 'POST':
        break;
    case 'DELETE':
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 取得所有電影資料
function getMovies()
{
    global $conn;
    $sql = 'SELECT * FROM `movies`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

function getTypeMovies($type, $show)
{
    global $conn;
    $sql = 'SELECT * FROM `movies` WHERE `show_status` = :show';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':show', $show);
    $stmt->execute();
    $data = [];
    $today = date('Y/m/d');
    if ($type == 'released') {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $movieDate = substr($row['play_date'], -10);
            if (strtotime($today) - strtotime($movieDate) >= 0) {
                $data[] = $row;
            }
        }
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $movieDate = substr($row['play_date'], -10);
            if (strtotime($today) - strtotime($movieDate) < 0) {
                $data[] = $row;
            }
        }
    }

    echo json_encode($data);
}

function setShowMovies($movieId, $show)
{
    global $conn;
    $movieId = intval($movieId);
    $sql = 'UPDATE `movies` SET `show_status` = :show WHERE `id` = :movieId';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':show', $show);
    $stmt->bindParam(':movieId', $movieId);
    if ($stmt->execute()) {
        if ($show) {
            $msg = '新增成功';
        } else {
            $msg = '移除成功';
        }
        $data = returnData(201, $msg);
    } else {
        $data = returnData(500, '伺服器內部問題', STATUS_500);
    }

    echo json_encode($data);
}