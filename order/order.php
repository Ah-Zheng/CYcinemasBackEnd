<?php
require_once '../header.php';
require_once '../database.php';

$url = explode("/",rtrim($_GET['url'],"/"));

if($url[0]){
    
    switch ($url[0]) {
        case 'getMovies':
            getMovies();
            break;
    
        case 'getMovieDay':
            getMovieDay($url[1]);
            break;
    
        case 'getMovieTime':
            getMovieTime($url[1]);
    
            break;
            
        case 'getTickets':
            getTickets();
            break;
            
        case 'getFoodDrinks':
            getFoodDrinks();
            
            break;
        case 'getScreeingID':
            getScreeingID($url[1],$url[2],$url[3]);
        
            break;
        default:
            break;
    }
}else{
    echo("What do you need?");
}

// $conn -> close();

function getMovies(){
    global $conn;
    // $result = $conn -> query("select movies.id,encoded_id,name from movies join movie_time on encoded_id = movies_encoded_id where rating is not null and theaters_name = '國賓影城@台北長春廣場' group by movies.id");
   
    $sql = "SELECT `movies`.`id`, `encoded_id`, `name` FROM `movies` join `movie_time` on `encoded_id` = `movies_encoded_id` where `rating` is not null and `theaters_name` = '國賓影城@台北長春廣場' group by `movies`.`id`";
    // $stmt = $conn->prepare($sql);
    // $stmt->bindParam(':id', $newsId);
    // $stmt->execute();
    $stmt = $conn->query($sql);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);

    // echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

function getMovieDay($id){
    global $conn;

    $encoded_id = htmlspecialchars($id);

    $sql = "SELECT `weekday`, `date` FROM `movie_day` JOIN `movies` ON `movies_encoded_id` = `encoded_id` where `movies_encoded_id` = :movies_encoded_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':movies_encoded_id', $encoded_id);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);

    // $result = $conn -> query("SELECT weekday,date FROM movie_day join movies on movies_encoded_id = encoded_id where movies_encoded_id = '$encoded_id'");
    // echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));

    
}

function getMovieTime($id){
    global $conn;

    $encoded_id = htmlspecialchars($id);

    $sql = "SELECT `time` FROM `movie_time` JOIN `movies` on `movies_encoded_id` = `encoded_id` where `movies_encoded_id` = :movies_encoded_id and theaters_name = '國賓影城@台北長春廣場'";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':movies_encoded_id', $encoded_id);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);

    // $result = $conn -> query("SELECT time FROM movie_time join movies on movies_encoded_id = encoded_id where movies_encoded_id = '$encoded_id' and theaters_name = '國賓影城@台北長春廣場'");
    // echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

function getTickets(){
    global $conn;

    $sql = "SELECT `name`, `price` FROM `tickets`";
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
}

function getFoodDrinks(){
    global $conn;

    $sql = "SELECT `name`, `size`, `price` FROM `food_drinks`";
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);

}

function getScreeingID($movieID,$movieTime,$movieDate){
    global $conn;

    $sql ="SELECT `id` FROM `screenings` WHERE `movies_encoded_id` = :movies_encoded_id AND `movie_time_time` = :movie_time_time AND `movie_day_date` = :movie_day_date";
    $stmt = $conn->prepare($sql);

    $movieID = trim($movieID);
    $movieTime = trim($movieTime);
    $movieDate = trim($movieDate);
    $stmt->bindParam(':movies_encoded_id', $movieID);
    $stmt->bindParam(':movie_time_time', $movieTime);
    $stmt->bindParam(':movie_day_date', $movieDate);
    $stmt->execute();    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data);
}
?>