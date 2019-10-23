<?php
require_once "./header.php";
require_once "./database.php";

function insertSeats($scrID,$alpha,$number){
    $text = "REPLACE INTO screening_seats (screenings_id,scr_seats_number,seat_name,available) VALUES ";
    $alphabet = range('A','Z');
    for($i=0;$i<$alpha;$i++){
        for($j=1;$j<=$number;$j++){
            $text .= "($scrID,'"."$scrID-"."$alphabet[$i]" . "${j}" . "','" . "$alphabet[$i]" . "${j}',1),";
        }
    }
    return substr($text,0,-1);
}


$stmt = $conn->query("SELECT `id`,`courts_id` FROM `screenings`");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$length = $stmt->rowCount();

$json = json_encode($result);
$json = json_decode($json);

$sql = "";

for($i=0;$i<$length;$i++){
    if($json[$i]->courts_id==1) 
        $sql = insertSeats($json[$i]->id,10,28);
    else if($json[$i]->courts_id==2) 
        $sql =  insertSeats($json[$i]->id,10,20);
    else if($json[$i]->courts_id==3) 
        $sql = insertSeats($json[$i]->id,9,17);

        $exec = $conn->query($sql);

        if(!$exec){
            $error = $conn->errorInfo();
             echo "插入失敗，錯誤訊息：".$error[2];
        }          
}



?>