<?php

require_once '../header.php';
// require_once '../function.php';
require_once '../database.php';

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', rtrim($_GET['url'], '/'));

switch ($method) {
    case 'POST':
        // echo $url[0];
        switch ($url[0]) {
            case 'members':
                saveNewMember();
                break;
            case 'login':
                checkLogin();
                break;
            case 'showUserData':
                getUserData();
                break;
            case 'saveEditData':
                saveEditData();
                break;
            case 'saveNewPwd':
                saveNewPwd();
                break;
            case 'showDetail':
                showDetail();
                break;
            case 'showPoint':
                showPoint();
                break;
            case 'resetPwd':
                resetPwd();
                break;
            default:
                echo 'ERROR';
        }
        break;
    case 'GET':
        // echo "GET SUCCESS, ";
        checkAccount();
        break;
    default:
        echo 'XXXX.';
}

function resetPwd()
{
    global $conn;
    $account = $_POST['resetAcc'];
    $email = $_POST['resetEmail'];
    $password = $_POST['resetPwd'];


    $accIsValid = preg_match('/^[A-Za-z0-9]{5,}$/', $account);
    $emailIsValid = preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/", $email);
    $pwdIsValid = preg_match('/^[A-Za-z0-9]{5,}$/', $password);

    // 通過驗證 -> 再檢查此帳號是否存在
    if ($accIsValid && $emailIsValid && $pwdIsValid) {

        // 帳號存在性驗證
        $sql = 'SELECT count(*) FROM `members` WHERE `account` = :account';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':account', $account);
        $stmt->execute();
        $rowCount = $stmt->fetchColumn();

        if ($rowCount == 1) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = 'UPDATE `members` SET `password` = :password WHERE `account` = :account';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':account', $account);

            if ($stmt->execute()) {
                echo '密碼重設成功';
            } else {
                echo '密碼重設失敗';
            }
        } else {
            echo '無此帳號，請確認輸入是否正確';
        }
    } else {
        echo '密碼重設失敗，請確認輸入是否正確';
    }

}

function showPoint()
{
    global $conn;
    $account = $_POST['account'];
    $sql = 'SELECT `id` FROM `members` WHERE `account` = :account';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $id = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $targetId = json_encode($id);
    $targetId = json_decode($targetId);

    $id = $targetId[0]->id;

    $sql = 'SELECT * FROM `point_record` WHERE `members_id` = :id GROUP BY `id` DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}

function showDetail()
{
    global $conn;
    $account = $_POST['account'];
    $sql = 'SELECT * FROM `order_details` WHERE `members_account` = :account GROUP BY `id` DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}

function saveNewPwd()
{
    global $conn;
    $nowAcc = $_POST['nowAcc'];
    $oldPwd = $_POST['oldPwd'];
    $newPwd = $_POST['newPwd'];
    $sql = 'SELECT `password` FROM `members` WHERE `account` = :nowAcc';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nowAcc', $nowAcc);
    $stmt->execute();
    $pwdInDb = $stmt->fetch(PDO::FETCH_ASSOC);

    // 修改會員密碼: 新密碼驗證規則
    $pwdIsValid = preg_match('/^[A-Za-z0-9]{5,}$/', $newPwd);

    // 舊帳號驗證正確 且新帳號通過REGEX -> 進入修改密碼程序  舊帳號驗證錯誤 OR 新帳號沒過REGEX -> 直接回傳錯誤訊息
    if (password_verify($oldPwd, $pwdInDb['password']) && $pwdIsValid) {
        $passwordHash = password_hash($newPwd, PASSWORD_BCRYPT);
        $sql = 'UPDATE `members` SET `password` = :password WHERE `account` = :nowAcc';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':nowAcc', $nowAcc);

        if ($stmt->execute()) {
            $data = 'success';
        } else {
            $data = 'failed';
        }
    } else {
        $data = 'failed';
    }
    echo json_encode($data);
}

function saveEditData()
{
    global $conn;
    $nowAcc = $_POST['nowAcc'];
    $newName = $_POST['newName'];
    $newEmail = $_POST['newEmail'];
    $newPhone = $_POST['newPhone'];

    // 修改會員資料: 各欄位驗證規則
    $nameIsValid = preg_match("/^[^.,\/#!$%\^&\*;:{}=\-_`~()@<>\s]{1,}$/", $newName);
    $emailIsValid = preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/", $newEmail);
    $phoneIsValid = preg_match("/^09\d{2}-?\d{3}-?\d{3}$/", $newPhone);

    if ($nameIsValid && $emailIsValid && $phoneIsValid) {
        $sql = 'UPDATE `members` SET `name` = :newName, `email` = :newEmail, `phone` = :newPhone WHERE `account` = :nowAcc';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newName', $newName);
        $stmt->bindParam(':newEmail', $newEmail);
        $stmt->bindParam(':newPhone', $newPhone);
        $stmt->bindParam(':nowAcc', $nowAcc);
        if ($stmt->execute()) {
            $data = 'success';
        } else {
            $data = 'failed';
        }
        echo json_encode($data);
    } else {
        echo '修改會員資料失敗，請確認輸入資料是否有誤';
    }
}

function getUserData()
{
    global $conn;
    $account = $_POST['account'];
    $sql = 'SELECT `name`,`account`,`password`,`email`,`phone` FROM `members` WHERE `account` = :account';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $fullData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($fullData);
}

function checkLogin()
{
    // echo "LOGIN";
    global $conn;
    $account = $_POST['account'];
    $password = $_POST['password'];
    // 檢查帳號 帳號正確後 再檢查密碼
    $sql = 'SELECT count(*) FROM `members` WHERE `account` = :account';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $accIsExist = $stmt->fetchColumn();
    if ($accIsExist == 1) {
        // 帳號正確 檢查密碼
        // 根據帳號抓出對應的密碼(hash後) -> 透過password_verify 將明文與密文比對 -> 相符 登入成功 不相符 登入失敗
        $sql = 'SELECT `password` FROM `members` WHERE `account` = :account';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':account', $account);
        $stmt->execute();
        $pwdInDb = $stmt->fetch(PDO::FETCH_ASSOC);

        // print_r($pwdIsExist);
        if (password_verify($password, $pwdInDb['password'])) {
            // 帳密正確 回傳使用者資料
            $sql = 'SELECT `name`,`account`,`email`,`phone` FROM `members` WHERE `account` = :account';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':account', $account);
            $stmt->execute();
            $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($userData);
        } else {
            // echo "ACC OO PWD XX";
            echo 'Failed';
        }
    } else {
        // echo "ACC XX END";
        echo 'Failed';
    }
}

function checkAccount()
{
    global $conn;
    $account = $_GET['url'];
    // echo $account;
    $sql = 'SELECT count(*) FROM `members` WHERE `account` = :account';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $rowCount = $stmt->fetchColumn();
    echo $rowCount; // 回傳查詢結果(查到的列數)
}

function saveNewMember()
{
    global $conn;
    $name = $_POST['name'];
    $account = $_POST['account'];
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // 各欄位驗證規則
    $nameIsValid = preg_match("/^[^.,\/#!$%\^&\*;:{}=\-_`~()@<>\s]{1,}$/", $name);
    $accIsValid = preg_match('/^[A-Za-z0-9]{5,}$/', $account);
    $pwdIsValid = preg_match('/^[A-Za-z0-9]{5,}$/', $password);
    $emailPattern = preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/", $email);
    $phonePattern = preg_match("/^09\d{2}-?\d{3}-?\d{3}$/", $phone);

    // 帳號存在性驗證
    $sql = 'SELECT count(*) FROM `members` WHERE `account` = :account';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':account', $account);
    $stmt->execute();
    $rowCount = $stmt->fetchColumn();

    if ($nameIsValid && $accIsValid && $pwdIsValid && $emailPattern && $phonePattern && $rowCount == 0) {
        $sql = 'INSERT INTO `members` (`name`, `account`, `password`, `email`, `phone`) VALUES (:name, :account, :password, :email, :phone)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':account', $account);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);

        if ($stmt->execute()) {
            echo '會員註冊成功';
        } else {
            echo '會員註冊失敗';
        }
    } else {
        echo '會員註冊資訊有誤，請再確認';
    }
}
