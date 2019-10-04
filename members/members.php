<?php
require_once '../header.php';
// require_once '../function.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));


switch ($method) {
    case 'POST':
        // echo $url[0];
        if ($url[0] == 'members') {
            saveNewMember();
        } elseif ($url[0] == 'login') {
            checkLogin();
        }else {
            echo 'ERROR';
        }
        // saveNewMember();
        break;
    case 'GET':
        // echo "GET SUCCESS, ";
        checkAccount();
        break;
    default:
        echo 'XXXX.';
}

function checkLogin() {
    // echo "LOGIN";
    global $conn;
    $account = $_POST['account'];
    $password = $_POST['password'];
    // 檢查帳號 帳號正確後 再檢查密碼
    $sql = "SELECT * FROM members WHERE account = '{$account}'";
    $result = $conn->query($sql);
    $accIsExist = $result->num_rows;
    if ($accIsExist == 1) {
        // 帳號正確 檢查密碼
        $sql = "SELECT * FROM `members` WHERE `account` = '{$account}' AND `password` = '{$password}'";
        $result = $conn->query($sql);
        $pwdIsExist = $result->num_rows;
        if ($pwdIsExist == 1) {
            // 帳密正確 檢查是否為管理員登入
            $sql = "SELECT `name`,`account`,`email`,`phone` FROM `members` WHERE `account` = '{$account}' AND `password` = '{$password}'";
            $result = $conn->query($sql);
            $isAdmin = $result->fetch_assoc();
            echo json_encode($isAdmin);
        } else {
            // echo "ACC OO PWD XX";
            echo "Failed";
        }
    } else {
        // echo "ACC XX END";
        echo "Failed";
    }

}

function checkAccount() {
    global $conn;
    $account = $_GET['url'];
    // echo $account;
    $sql = "SELECT * FROM `members` WHERE `account` = '{$account}'";
    $result = $conn->query($sql);
    echo $result->num_rows; // 回傳查詢結果(查到的列數)
}

function saveNewMember() {
    // echo "CREATE NEW MEMBER";
    global $conn;
    $name = $_POST['name'];
    $account = $_POST['account'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = 'INSERT INTO `members` (`name`, `account`, `password`, `email`, `phone`) VALUES (?, ?, ?, ? ,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $name, $account, $password, $email, $phone);

    if ($stmt->execute()) {
        echo "會員註冊成功";
    } else {
        echo "會員註冊失敗";
    }

    $conn->close();
    // echo json_encode($data);
}
