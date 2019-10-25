<?php
/*
@ 報表資料
    - 本周總票數
    - 電影類型
    - 訂票時段
    - 餐點銷量 *
    - 票種
*/

require_once '../header.php';
require_once '../function.php';
require_once '../database.php';
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'GET':
        if (isset($url[0]) && $url[0] == 'ticketType') {
            if (isset($url[1]) && $url[1] == 'ticketData') {
                getTicketData();
                exit();
            }
            getTicketType();
        } elseif (isset($url[0]) && $url[0] == 'turnover') {
            getTurnover();
            exit();
        }
        break;
    case 'POST':
        break;
    case 'DELETE':
        break;
    default:
        echo json_encode(returnData(404, '404 NOT FOUND', STATUS_404));
        break;
}

/* 票種 */

// 取得票種
function getTicketType()
{
    global $conn;
    $sql = 'SELECT `name` FROM `tickets`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

// 取得票種數據
function getTicketData()
{
    global $conn;
    $sql = 'SELECT `tickets_num` FROM `order_details`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

/* 票種 -> 結束 */

/* 營業額 */

// 取得訂單內總金額
function getTurnover()
{
    global $conn;
    $sql = 'SELECT `total_price`, `datetime` FROM `order_details`';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

/* 營業額 -> 結束 */
