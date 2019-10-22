<?php
require_once '../header.php';
require_once '../database.php';

$url = explode("/",rtrim($_GET['url'],"/")); 


if($url[0]){
    
    switch ($url[0]) {
        
        case 'saveOrder':
            saveOrderDetail();
            break;

        case 'updateMemberPoint':
            updateMemberPoint();
            break;

        case 'getSellOut':
            getSellOut();
            break;

        case 'tapGetSellOut':
            tapGetSellOut();
            break;

        case 'getScreeningSeat':
            getScreeningSeat($url[1]);
            break;

        case 'lockScreeningSeat':
            lockScreeningSeat();
            break;

        case 'unlockScreeningSeat':
            unlockScreeningSeat();
                break;    
        
        default:
            break;
    }
}else{
    echo("What do you need?");
}

// $conn -> close();
function tapGetSellOut(){
    global $conn;
     // ------------------SELECT-------------------
     $ID = isset($_POST['ID'])?$_POST['ID']:-1;  
     $time1 = isset($_POST['time1'])?$_POST['time1']: -1;  
     if($ID == -1)
        echo "No  POST ID";  
     if($time1 == -1)
        echo "No  POST time1";  
     $sql = 'SELECT FROM_UNIXTIME(:time1)';  
     $stmt=$conn->prepare($sql); 
     $stmt->bindParam(':time1', $time1);  
     $stmt->execute();
     $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC); 
     $str =(string)$time1;
    //  echo $str;
     $time1 = $sqlData[0]["FROM_UNIXTIME('".$str."')"];
    //  var_dump($sqlData) ;
    //  echo $time1;



     $sql = 'SELECT `seat`FROM `order_details` WHERE (`screenings_id`=:id) AND (`datetime`>:time1)'; 
     $stmt=$conn->prepare($sql); 
     $stmt->bindParam(':id', $ID);
     $stmt->bindParam(':time1', $time1);  
     $stmt->execute();
     $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC); 

     echo json_encode($sqlData); 
     if(!$sqlData) 
        echo "no data";
}
function getSellOut(){
    global $conn;
     // ------------------SELECT-------------------
     $ID = isset($_POST['ID'])?$_POST['ID']:-1;  
     if($ID == -1)
        echo "No  POST ID";  
     $sql = 'SELECT `seat` FROM `order_details`';   
     if($ID) 
         $sql = 'SELECT `seat`, UNIX_TIMESTAMP(datetime) `time1` FROM `order_details` WHERE `screenings_id`=:id'; 
     $stmt=$conn->prepare($sql);
     if($ID)
         $stmt->bindParam(':id', $ID);
     $stmt->execute();
     $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC);
     echo json_encode($sqlData);  
} 
// ----------------saveOrderDetail---------------
function saveOrderDetail(){ 
    global $conn;
    $mysql = $_POST['SQL'];  
    switch ($mysql) {
        case 'show': 
        // ------------------show tables------------------- 
        $sql = 'SHOW tables';   
        $stmt=$conn->prepare($sql); 
        $stmt->execute();
        $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $showTable = [];
        foreach ($sqlData as $key => $value) { 
            array_push($showTable,$value["Tables_in_ahzheng_cy_cinemas"]);  
        }  
        echo "*******************showTables**********************";  
        echo json_encode($sqlData);
            break;
        case 'desc':
            // ------------------Check Field-------------------    
            $sql = 'DESC `order_details`';   
            $stmt=$conn->prepare($sql); 
            $stmt->execute();
            $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $desc = [];
            foreach ($sqlData as $key => $value) { 
                array_push($desc,$value["Field"]); 
            }  
            echo "*********************fields************************"; 
            echo json_encode($desc);   
            break;
        case 'select':
            // ------------------SELECT-------------------
            $ID = isset($_POST['ID'])?$_POST['ID']:0;    
            $sql = 'SELECT * FROM `order_details`';   
            if($ID) 
                $sql = 'SELECT * FROM `order_details` WHERE `id`=:id'; 
            $stmt=$conn->prepare($sql);
            if($ID)
                $stmt->bindParam(':id', $ID);
            $stmt->execute();
            $sqlData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($sqlData); 
            break;
        case 'save':  
        // ----------------------save-----------------------
            $frontData = isset($_POST['JSONData'])?$_POST['JSONData']:'no post';
            $list = json_decode($frontData);    
            $ticketsNum      = isset($_POST['ticketData'])?$_POST['ticketData']:'no post'; 
            $foodDrinksNum   = isset($_POST['foodData'])?$_POST['foodData']:'no post';
            $account         = $list->account == ""?"Guest":$list->account;    

            $sql = 'INSERT INTO `order_details` ( 
             `screenings_id`,
             `serial_number` ,
             `members_account` ,
             `courts_id`,
             `seat` ,
             `total_price` ,
             `discounted_price` ,
             `tickets_total_num` ,
             `tickets_num` ,
             `meals_num` ,
             `name` ,
             `phone` ,
             `email` ) 
            VALUES (:a,:b,:c,:d,:e,:f,:g,:m,:h,:i,:j,:k,:l)'; 
            $a=$_POST['screeningID'];   
            $c=$_POST['courts_id'];  
            $tickets_total_num =$_POST['ticketTotalNum'];
            $stmt = $conn->prepare($sql); 
            $stmt->bindParam(':a',$a);
            $stmt->bindParam(':b',$list->orderNumber); 
            $stmt->bindParam(':c',$account); 
            $stmt->bindParam(':d',$c); 
            $stmt->bindParam(':e',$list->seat); 
            $stmt->bindParam(':f',$list->total); 
            $stmt->bindParam(':g',$list->real); 
            $stmt->bindParam(':m',$tickets_total_num); 
            $stmt->bindParam(':h',$ticketsNum); 
            $stmt->bindParam(':i',$foodDrinksNum); 
            $stmt->bindParam(':j',$list->memberName); 
            $stmt->bindParam(':k',$list->phone); 
            $stmt->bindParam(':l',$list->email);
            $stmt->execute(); 
            echo "Saved order details";
            break; 
        default: 
            break;
    }
}
// ----------------saveOrderDetail--------------- 

function updateMemberPoint(){
    global $conn;

    // 更新會員點數
    $frontData = isset($_POST['JSONData'])?$_POST['JSONData']:'no post';
    $list = json_decode($frontData);    
    $account         = $list->account;    
    $point = floor($list->real/100);

    $sql = "UPDATE `members` SET `point` = `point` + :pt WHERE `account` = :account";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pt',$point);
    $stmt->bindParam(':account',$account);
    $exec = $stmt->execute();

    if($exec){
        echo "update memberPoint OK, ";
    }else{
        echo "update failed: " . $conn->errorInfo()[2];
    }

    // 新增會員更新點數紀錄
    $stmt = $conn->query("SELECT `id`,`point` FROM `members` WHERE account = '$list->account'");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $json = json_encode($result);
    $json = json_decode($json);


    $id = $json[0]->id;
    $cuPoint = $json[0]->point;
    $sql = "INSERT INTO `point_record` (`members_id`, `update_point`, `current_point`, `desc`) VALUES
     (:members_id,:update_point,:current_point, '購票獲得點數')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':members_id',$id);
    $stmt->bindParam(':update_point',$point);
    $stmt->bindParam(':current_point',$cuPoint);

    $exec = $stmt->execute();

    if($exec){
        echo "insert point_record OK";
    }else{
        echo "insert failed: " . $conn->errorInfo()[2];
    }

}


function getScreeningSeat($scrID=''){
    global $conn;
    if($scrID==''){
        $sql = "SELECT * FROM screening_seats";
        $stmt = $conn->query($sql);
        if($stmt){
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            var_dump($data);
        }else{
            $error = $conn->errorInfo();
            echo "查詢失敗，錯誤訊息：".$error[2];
        }
    }else{
        $sql = "SELECT * FROM screening_seats WHERE `screenings_id` = $scrID";
        $stmt = $conn->query($sql);
        if($stmt){
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            var_dump($data);
        }else{
            $error = $conn->errorInfo();
            echo "查詢失敗，錯誤訊息：".$error[2];
        }
    }
}

function lockScreeningSeat(){
    global $conn;
    $screenings_id = isset($_POST['screeningID'])?$_POST['screeningID']:'no post';
    $choosedSeat = isset($_POST['choosedSeat'])?$_POST['choosedSeat']:'no post';

    //經過處理後變成'A1','A2',...的形式才能夠放進查詢子句
    $seatNumber = count(explode(",",$choosedSeat));
    $seatName = str_replace(",","','",$choosedSeat);
    $seatName = "'$seatName'";

    $conn->beginTransaction();

    $sql = "SELECT * FROM `screening_seats` WHERE `screenings_id` = '$screenings_id' AND `seatName` IN ($seatName) AND `available` = 1 FOR UPDATE";
    $check = $conn->query($sql);
    // if($check){
    //     echo "OK"."<br>";
    //     echo var_dump($check->fetchAll(PDO::FETCH_ASSOC));
    // }else{
    //     echo "not OK";
    // }

    if($check->rowCount() >= $seatNumber){      //先檢查這些位置是不是還是空的
        $sql = "UPDATE `screening_seats` SET `available` = 0 WHERE `screenings_id` = '$screenings_id' AND `seatName` IN ($seatName)";
        $exec = $conn->query($sql);
        if($exec){
            echo "update screening_seats OK.";
            $conn->commit();
        }else{
            echo "update failed: " . $conn->errorInfo()[2];
            $conn->rollBack();
            exit();
        }
    }else{
        echo "there are not enough seats."; 
        $conn->rollBack();
    }
}

function unlockScreeningSeat(){
    global $conn;
    $screenings_id = isset($_POST['screeningID'])?$_POST['screeningID']:'no post';
    $choosedSeat = isset($_POST['choosedSeat'])?$_POST['choosedSeat']:'no post';

    //經過處理後變成'A1','A2',...的形式才能夠放進查詢子句
    $seatNumber = count(explode(",",$choosedSeat));
    $seatName = str_replace(",","','",$choosedSeat);
    $seatName = "'$seatName'";

    $sql = "SELECT * FROM `screening_seats` WHERE `screenings_id` = '$screenings_id' AND `seatName` IN ($seatName) AND `available` = 0";
    $check = $conn->query($sql);
        if($check->rowCount() >= $seatNumber){      //先檢查這些位置數對不對

        $sql = "UPDATE `screening_seats` SET `available` = 1 WHERE `screenings_id` = '$screenings_id' AND `seatName` IN ($seatName)";
        $exec = $conn->query($sql);
        if($exec){
            echo "recover screening_seats OK.";
        }else{
            echo "recover failed: " . $conn->errorInfo()[2];
            exit();
        }
    }else{
        echo "there are no seats need to recover.";
    }
}

?>
