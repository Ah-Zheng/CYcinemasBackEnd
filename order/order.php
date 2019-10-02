<?php
require_once './header.php';
require_once './database.php';

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
        default:
            break;
    }
}else{
    echo("What do you need?");
}

mysqli_close($conn);

function getMovies(){
    global $conn;
    $result = $conn -> query("select movies.id,encoded_id,name from movies join movie_time on encoded_id = movies_encoded_id where rating is not null and theaters_name = '國賓影城@台北長春廣場' group by movies.id");

    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

function getMovieDay($id){
    global $conn;

    $encoded_id = htmlspecialchars($id);

    $result = $conn -> query("SELECT weekday,date FROM movie_day join movies on movies_encoded_id = encoded_id where movies_encoded_id = '$encoded_id'");
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

function getMovieTime($id){
    global $conn;

    $encoded_id = htmlspecialchars($id);

    $result = $conn -> query("SELECT time FROM movie_time join movies on movies_encoded_id = encoded_id where movies_encoded_id = '$encoded_id' and theaters_name = '國賓影城@台北長春廣場'");
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

function getTickets(){
    global $conn;

    $result = $conn -> query("SELECT name,price FROM tickets");
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}

function getFoodDrinks(){
    global $conn;
    $result = $conn -> query("SELECT name,size,price FROM food_drinks");
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));

}
?>