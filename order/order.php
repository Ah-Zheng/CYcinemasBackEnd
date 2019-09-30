<?php
require_once '../header.php';
require_once '../database.php';



switch ($_GET['fields']) {
    case 'getMovies':
        $result = $conn -> query("select movies.id,encoded_id,name from movies join movie_time on encoded_id = movies_encoded_id where rating is not null and theaters_name = '國賓影城@台北長春廣場' group by movies.id");

        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        break;

    case 'getMovieDay':
        $encoded_id = $_GET['encoded_id'];

        $result = $conn -> query("SELECT weekday,date FROM movie_day join movies on movies_encoded_id = encoded_id where movies_encoded_id = '$encoded_id'");
        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        break;

    case 'getMovieTime':
        $encoded_id = $_GET['encoded_id'];

        $result = $conn -> query("SELECT time FROM movie_time join movies on movies_encoded_id = encoded_id where movies_encoded_id = '$encoded_id' and theaters_name = '國賓影城@台北長春廣場'");
        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        break;
        
    case 'getTickets':
        $result = $conn -> query("SELECT name,price FROM tickets");
        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        break;
        
    case 'getFoodDrinks':
        $result = $conn -> query("SELECT name,size,price FROM food_drinks");
        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        break;
    default:
        break;
}

mysqli_close($conn);

?>