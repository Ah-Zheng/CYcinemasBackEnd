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
        } elseif (isset($url[0])) {
            getMovies($url[0]);
            exit();
        }
        getMovies();
        break;
    case 'POST':
        if (isset($url[0]) && $url[0] == 'update') {
            updateMovies($url[1]);
        }
        break;
    case 'DELETE':
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

// 取得電影資料
function getMovies($movieId = '')
{
    global $conn;
    if ($movieId == '') { // 判斷有無電影id，沒有就取得所有電影資料
        $sql = 'SELECT DISTINCT m.`id`,m.`encoded_id`, m.`name`, m.`enname`, m.`rating`, m.`run_time`, m.`info`, m.`actor`, m.`genre`, m.`play_date`, m.`poster`, m.`trailer`, m.`show_status` FROM `movies` m JOIN `movie_time` mt ON m.`encoded_id` = mt.`movies_encoded_id` WHERE mt.`theaters_name` = "國賓影城@台北長春廣場"';
        $stmt = $conn->prepare($sql);
    } else {
        $movieId = intval($movieId);
        $sql = 'SELECT * FROM `movies` WHERE `id` = :movieId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':movieId', $movieId);
    }
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

function getTypeMovies($type, $show)
{
    global $conn;
    if ($type == 'released') {
        $sql = 'SELECT DISTINCT m.`id`,m.`encoded_id`, m.`name`, m.`enname`, m.`rating`, m.`run_time`, m.`info`, m.`actor`, m.`genre`, m.`play_date`, m.`poster`, m.`trailer`, m.`show_status` FROM `movies` m JOIN `movie_time` mt ON m.`encoded_id` = mt.`movies_encoded_id` WHERE mt.`theaters_name` = "國賓影城@台北長春廣場" AND `show_status` = :show';
    } else {
        $sql = 'SELECT * FROM `movies` WHERE `show_status` = :show';
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':show', $show);
    $stmt->execute();
    $data = [];
    $today = date('Y/m/d');
    if ($type == 'released') {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['rating'] != '') {
                // if (strtotime($today) - strtotime($row['play_date']) >= 0) {
                $data[] = $row;
            }
        }
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['rating'] == '') {
                // if (strtotime($today) - strtotime($row['play_date']) < 0) {
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

// 更新電影資料
function updateMovies($movieId)
{
    global $conn;
    $movieId = intval($movieId);
    $movie = isset($_POST['movieDatas']);
    $sql = 'UPDATE `movies` SET `name` = :movieName, `actor` = :actor, `genre` = :genre, `rating` = :rating, `run_time` = :runTime, `play_date` = :playDate, `info` = :info WHERE `id` = :movieId';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':movieName', $movie->name);
    $stmt->bindParam(':actor', $movie->actor);
    $stmt->bindParam(':genre', $movie->genre);
    $stmt->bindParam(':rating', $movie->rating);
    $stmt->bindParam(':runTime', $movie->run_time);
    $stmt->bindParam(':playDate', $movie->play_date);
    $stmt->bindParam(':info', $movie->info);
    $stmt->bindParam(':movieId', $movieId);
    if ($stmt->execute()) {
        $data = returnData(201, '修改成功');
    } else {
        $data = returnData(500, '伺服器內部錯誤', STATUS_500);
    }

    echo json_encode($data);
}
