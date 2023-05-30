<?php
// error_reporting(0);

header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: PUT');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Authorization, X-Request-With');

include('function.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];


if($requestMethod == 'PUT'){
    $inputData = json_decode(file_get_contents("php://input"), true);
    if(empty($inputData)){

        $updateScore = updateScore($_POST, $_GET);
    }else{

        $updateScore = updateScore($inputData, $_GET);

    }
    echo  $updateScore;
    

}else{

    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

?>