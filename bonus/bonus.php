<?php
require_once "../database.php";   
require_once "../header.php";

$url = explode("/",rtrim($_GET['url'],"/"));

if($url[0]){
    switch($url[0]){
        case 'getProbability':
            getProbability();
            break;
        case 'getMemberPoint':
            getMemberPoint($url[1]);
            break;
        case 'updateMemberPoint':
            updateMemberPoint($url[1]);
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

function updateMemberPoint($acc){
    global $conn;
    $account = isset($_POST['account'])?$_POST['account']:'no post';
    $current_point = isset($_POST['current_point'])?$_POST['current_point']:'no post';
    $update_point = isset($_POST['update_point'])?$_POST['update_point']:'no post';

    // 更新會員點數

        $conn->beginTransaction();

        $sql = "SELECT * FROM `members` WHERE `account` = '$account' and `point` + $update_point >=0";
        $check = $conn->query($sql);
        $check = $check->fetch();
        if($check){
            $sql = "UPDATE `members` SET `point` = `point` + :update_point WHERE `account` = :account";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':update_point',$update_point);
            $stmt->bindParam(':account',$account);
            $exec = $stmt->execute();
        
            if($exec){
                echo "update memberPoint OK, ";
            }else{
                echo "update failed: " . $conn->errorInfo()[2];
                $conn->rollBack();
                exit();
            }
        }else{
            echo "Point is not enough.";
            $conn->rollBack();
            exit();
        }
    
        // 新增會員更新點數紀錄
        $stmt = $conn->query("SELECT `id`,`point` FROM `members` WHERE account = '$account'");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $json = json_encode($result);
        $json = json_decode($json);
    
    
        $id = $json[0]->id;

        $sql = "INSERT INTO `point_record` (`members_id`, `update_point`, `current_point`, `desc`) VALUES
         (:members_id,:update_point,:current_point, '玩小瑪莉的點數異動')";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':members_id',$id);
        $stmt->bindParam(':update_point',$update_point);
        $stmt->bindParam(':current_point',$current_point);      //更新後點數
    
        $exec = $stmt->execute();
    
        if($exec){
            echo "insert point_record OK";
            $conn->commit();
        }else{
            echo "insert failed: " . $conn->errorInfo()[2];
            $conn->rollBack();
            exit();
        }
    
}

function getProbability(){
    $probabilty = new stdClass();
    $probabilty->winOdds = 0.8;     //骰到非XX的機率
    
    //各倍率中的機率，應和為1。依序是爆米花、飲料、套餐、5倍BAR、可愛爆米花、7倍BAR和10倍BAR
    $probabilty->bettingOddsArray = [0.4, 0.3, 0.2, 0.5, 0.03, 0.02, 0.01];    
    echo json_encode([$probabilty]);
}
?>