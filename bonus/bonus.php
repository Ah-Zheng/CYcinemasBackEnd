<?php
require_once "../database.php";   
require_once "../header.php";

$url = explode("/",rtrim($_GET['url'],"/"));

if($url[0]){
    switch($url[0]){
        case 'getMemberPoint':
            getMemberPoint($url[1]);
            break;
        default:
            break;
    }
}else{
    echo "What do yooooou need?";
}

function getMemberPoint($acc=''){
    global $conn;
    if(!$acc){
        $sql = "SELECT * FROM `members`";
        $exec = $conn->query($sql);
        if($exec)
        {       
            $data = $exec->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        }
        else
        {
            $error = $conn->errorInfo();
            echo "查詢失敗，錯誤訊息：".$error[2];
        }
    }else{
        $sql = "SELECT `point` FROM `members` WHERE `account` = :acc";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":acc",$acc);
        $exec = $stmt->execute();
            if($exec)
            {       
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($data);
            }
            else
            {
                $error = $conn->errorInfo();
                echo "查詢失敗，錯誤訊息：".$error[2];
            }
    }

}

?>