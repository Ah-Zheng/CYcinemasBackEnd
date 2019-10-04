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
    // $sql = "SELECT count(*) FROM `members` WHERE `account` = :account";
    // $stmt = $conn->prepare($sql);
    // $stmt->bindParam(':account', $account);
    // $stmt->execute();
    // $rowCount = $stmt->fetchColumn();
    // echo $rowCount; // 回傳查詢結果(查到的列數)
    // 檢查帳號 帳號正確後 再檢查密碼
    $sql = "SELECT count(*) FROM `members` WHERE `account` = :account";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $accIsExist = $stmt->fetchColumn();
    if ($accIsExist == 1) {
        // 帳號正確 檢查密碼
        $sql = "SELECT count(*) FROM `members` WHERE `account` = :account AND `password` = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':account', $account);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $pwdIsExist = $stmt->fetchColumn();
        if ($pwdIsExist == 1) {
            // 帳密正確 回傳使用者資料
            $sql = "SELECT `name`,`account`,`email`,`phone` FROM `members` WHERE `account` = :account AND `password` = :password";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':account', $account);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($userData);
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
    $sql = "SELECT count(*) FROM `members` WHERE `account` = :account";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $rowCount = $stmt->fetchColumn();
    echo $rowCount; // 回傳查詢結果(查到的列數)
}

function saveNewMember() {
    // echo "CREATE NEW MEMBER";
    global $conn;
    $name = $_POST['name'];
    $account = $_POST['account'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = 'INSERT INTO `members` (`name`, `account`, `password`, `email`, `phone`) VALUES (:name, :account, :password, :email, :phone)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':account', $account);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);

    if ($stmt->execute()) {
        echo "會員註冊成功";
    } else {
        echo "會員註冊失敗";
    }

    // $conn->close();
    // echo json_encode($data);
}
