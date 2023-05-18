<?php

require '../inc/dbcon.php';

function error422($message){

    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function storeUser($userInput){

    global $conn;

    //$id = mysqli_real_escape_string($conn, $userInput['id']);
    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);

    if(empty(trim($username))){

        return error422('Enter your username');
    }
    elseif(empty(trim($password))){

        return error422('Enter your password');
    }

    else{

        $query = "INSERT INTO users (username, password) VALUES ( '$username','$password')";
        $result = mysqli_query($conn, $query);

        if($result){

            $data = [
                'status' => 201,
                'message' => 'User Created Successfully',
            ];
            header("HTTP/1.0 201 Created");
            return json_encode($data);

        }else{
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }
   
}


function getUserList(){

    global $conn;

    $query = "SELECT * FROM users";
    $query_run = mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'User List Fetched Successfully',
                'data' => $res,
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        }
        else{
            $data = [
                'status' => 404,
                'message' => 'No User Found',
            ];
            header("HTTP/1.0 404 No User Found");
            return json_encode($data);
        }
    }
    else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}
function getUser($userParams){

    global $conn;
    if($userParams['id'] == null){

        return error422('Enter your id');
    }
    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    $query = "SELECT * FROM users WHERE id='$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result)==1){

            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'User Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No User Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }

    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

function updateUser($userInput, $userParams){

    global $conn;

    if(!isset($userParams['id'])){

        return error422('user id not found in URL');

    }elseif($userParams['id'] == null){

        return error422('Enter the user id');
    }

    $userId = mysqli_real_escape_string($conn, $userParams['id']);

    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);

    if(empty(trim($username))){

        return error422('Enter your username');
    }
    elseif(empty(trim($password))){

        return error422('Enter your password');
    }

    else{

        $query = "UPDATE users SET username='$username', password='$password' WHERE id='$userId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if($result){

            $data = [
                'status' => 200,
                'message' => 'User Updated Successfully',
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);

        }else{
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }
   
}

function deleteUser($userParams){

    global $conn;

    if(!isset($userParams['id'])){

        return error422('user id not found in URL');

    }elseif($userParams['id'] == null){

        return error422('Enter the user id');
    }

    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    $query = "DELETE FROM users WHERE id='$userId' LIMIT 1";
    $result = mysqli_query($conn,$query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'User Deleted Successfully',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    }else{

        $data = [
            'status' => 404,
            'message' => 'User Not found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}

?>