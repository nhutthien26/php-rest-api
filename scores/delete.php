<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Authorization, X-Request-With');

include('function.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == "DELETE"){

    $deleteScore = ($_GET);
        echo $deleteScore;
 

}

/*if($requestMethod == "GET"){

    if(isset($_GET['id_song'])){

        $score = getScore($_GET);
        echo $score;
    }else{
        $scoreList = getScoreList();
        echo $scoreList;
    }
   

}*/

else{
    $data = [
        'status' => 405,
        'message' => $requestMethod. 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

?>