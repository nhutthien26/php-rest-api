<?php

require '../inc/dbcon.php';

//error422
function error422($message){

    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function storeSong($songInput){

    global $conn;

    $name = mysqli_real_escape_string($conn, $songInput['name']);
    $data = mysqli_real_escape_string($conn, $songInput['data']);
    $image = mysqli_real_escape_string($conn, $songInput['image']);
    $mp3 = mysqli_real_escape_string($conn, $songInput['mp3']);



    if(empty(trim($name))){

        return error422('Enter your name');
    }elseif(empty(trim($data))){

        return error422('Enter your data');
    }elseif(empty(trim($image))){

        return error422('Enter your image');

    }elseif(empty(trim($mp3))){
        return error422('Enter your mp3');

    }

    else{
        $query = "INSERT INTO songs (name, data, image, mp3) VALUES ('$name', '$data', '$image', '$mp3')";
        $result = mysqli_query($conn, $query);

        if($result){

            $data = [
                'status' => 201,
                'message' => 'Song Created Successfully',
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


function getSongList(){


    global $conn;

    $query = "SELECT * FROM songs";
    $query_run = mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run)> 0 ){
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            $data = [
                'status' => 200,
                'message' => 'Song List Fetch Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);

        }else{

            $data = [
                'status' => 404,
                'message' => 'No Song Found',
            ];
            header("HTTP/1.0 404 No Song Found");
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


function getSong($songParams){

    global $conn;

    if($songParams['id'] == null){

        return error422('Enter your song id');
    }
    $songId = mysqli_real_escape_string($conn, $songParams['id']);

    $query = "SELECT * FROM songs WHERE id='$songId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){

        if(mysqli_num_rows($result) == 1){

            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Song Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);

        }else{

            $data = [
                'status' => 404,
                'message' => 'No Song Found',
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

function updateSong($songInput, $songParams){

    global $conn;

    if(!isset($songParams['id'])){
        return error422('Song id not found in URL');

    }elseif($songParams['id'] == null){

        return error422('Enter the song id');

    }
    $songId = mysqli_real_escape_string($conn, $songParams['id']);

    $name = mysqli_real_escape_string($conn, $songInput['name']);
    $data = mysqli_real_escape_string($conn, $songInput['data']);
    $image = mysqli_real_escape_string($conn, $songInput['image']);
    $mp3 = mysqli_real_escape_string($conn, $songInput['mp3']);



    if(empty(trim($name))){

        return error422('Enter your name');
    }elseif(empty(trim($data))){

        return error422('Enter your data');
    }elseif(empty(trim($image))){

        return error422('Enter your image');

    }elseif(empty(trim($mp3))){
        return error422('Enter your mp3');

    }

    else{
        $query = "UPDATE songs SET name='$name', data='$data', image='$image', mp3='$mp3' WHERE id='$songId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if($result){

            $data = [
                'status' => 200,
                'message' => 'Song Updated Successfully',
            ];
            header("HTTP/1.0 200 Updated");
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

function deleteSong($songParams){

    global $conn;

    if(!isset($songParams['id'])){
        return error422('Song id not found in URL');

    }elseif($songParams['id'] == null){

        return error422('Enter the song id');

    }
    $songId = mysqli_real_escape_string($conn, $songParams['id']);

    $query = "DELETE FROM songs WHERE id='$songId' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Song Deleted Successfully ',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);

    }else{

        $data = [
            'status' => 404,
            'message' => 'Song Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}

?>
