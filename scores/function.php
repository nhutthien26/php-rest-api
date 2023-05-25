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


//READ SCORES
function getScoreList(){

    global $conn;

    $query = "SELECT * FROM scores";
    $query_run = mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Score List Fetched Successfully',
                'data' => $res,
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        }
        else{
            $data = [
                'status' => 404,
                'message' => 'No Score Found',
            ];
            header("HTTP/1.0 404 No Score Found");
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

//CREATE SCORES

function storeScore($scoreInput){

    global $conn;

    $id_song = mysqli_real_escape_string($conn, $scoreInput['id_song']);
    $score = mysqli_real_escape_string($conn, $scoreInput['score']);
    $star = mysqli_real_escape_string($conn, $scoreInput['star']);
   



    if(empty(trim($id_song))){

        return error422('Enter your id song');
    }elseif(empty(trim($score))){

        return error422('Enter your score');
    }elseif(empty(trim($star))){

        return error422('Enter your star');

    }

    else{
        $query = "INSERT INTO scores (id_song, score, star) VALUES ('$id_song', '$score', '$star')";
        $result = mysqli_query($conn, $query);

        if($result){

            $data = [
                'status' => 201,
                'message' => 'Score Created Successfully',
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


//GET SCORE WITH ID

function getScore($scoreParams){

    global $conn;

    if($scoreParams['id_user'] == null){

        return error422('Enter your song id_user');
    }
    $scoreId = mysqli_real_escape_string($conn, $scoreParams['id_user']);

    $query = "SELECT * FROM scores WHERE id_user='$scoreId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){

        if(mysqli_num_rows($result) == 1){

            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);

        }else{

            $data = [
                'status' => 404,
                'message' => 'No Score Found',
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

//UPDATE SCORE 

function updateScore($scoreInput, $scoreParams)
{
    global $conn;

    if (!isset($scoreParams['id_user'])) {
        return error422('user id not found in URL');
    } elseif ($scoreParams['id_user'] == null) {
        return error422('Enter the user id');
    }

    $scoreId = mysqli_real_escape_string($conn, $scoreParams['id_user']);

    $id_song = mysqli_real_escape_string($conn, $scoreInput['id_song']);
    $score = mysqli_real_escape_string($conn, $scoreInput['score']);
    $star = mysqli_real_escape_string($conn, $scoreInput['star']);

    if (empty(trim($id_song))) {
        return error422('Enter your id song');
    } elseif (empty(trim($score))) {
        return error422('Enter your score');
    } elseif (empty(trim($star))) {
        return error422('Enter your star');
    } else {
        $query = "UPDATE scores SET id_song='$id_song', score='$score', star='$star' WHERE id_user='$scoreId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $data = [
                'status' => 200,
                'message' => 'Score Updated Successfully',
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }
}


//DELETE SCORE

function deleteScore($scoreParams){

    global $conn;

    if(!isset($scoreParams['id_user'])){

        return error422('user id not found in URL');

    }elseif($scoreParams['id_user' ] == null){
        
        return error422('Enter the user id');
    }

    $scoreId = mysqli_real_escape_string($conn, $scoreParams['id_user']);

    $query = "DELETE FROM scores WHERE id_user = '$scoreId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Score Deleted Successfully',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);

    }else{

        $data = [
            'status' => 404,
            'message' => 'Score Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }

}

// GET ID_SONG TO DISPLAY USER


function getUserWithIDSong($scoreParams){
    global $conn;

    if($scoreParams['id_song'] == null){
        return error422('Enter your id song');
    }

    $idSong = mysqli_real_escape_string($conn, $scoreParams['id_song']);

    // Truy vấn dữ liệu từ bảng scores và join với bảng users
    $query = "SELECT users.username, scores.score, scores.star
              FROM scores
              INNER JOIN users ON scores.id_user = users.id
              WHERE scores.id_song = '$idSong'";

    // Thực hiện truy vấn
    $result = mysqli_query($conn, $query);
    if ($result) {
        // Mảng để lưu danh sách username
        $usernames = array();
        while ($row = mysqli_fetch_assoc($result)) {
            // Lưu username vào mảng
            $user = [
                'username' => $row['username'],
                'score' => $row['score'],
                'star' => $row['star']

            ];
            $users[] = $user;           
        }   
        if (!empty($users)) {
            $data = [
                'status' => 200,
                'message' => 'Users Fetched Successfully',
                'data' => $users
            ];
            header("HTTP/1.0 200 OK");
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


//Ranking User By Score

function getRankedPlayers($scoreParams) {
    global $conn;

    if($scoreParams['id_song'] == null){
        return error422('Enter your id song');
    }

    $idSong = mysqli_real_escape_string($conn, $scoreParams['id_song']);

    // Truy vấn dữ liệu từ bảng scores và join với bảng users
    $query = "SELECT users.username, scores.score, scores.star
              FROM scores
              INNER JOIN users ON scores.id_user = users.id
              WHERE scores.id_song = '$idSong'";

    // Thực hiện truy vấn
    $result = mysqli_query($conn, $query);
    if ($result) {
        // Mảng để lưu danh sách username
        $usernames = array();
        while ($row = mysqli_fetch_assoc($result)) {
            // Lưu username vào mảng
            $user = [
                'username' => $row['username'],
                'score' => $row['score'],
                'star' => $row['star']

            ];
            $users[] = $user;           
        }   
        if (!empty($users)) {
            // Sắp xếp mảng theo điểm số giảm dần
            usort($users, function($a, $b) {
                return $b['score'] - $a['score'];
            });
    
            // Gán hạng cho mỗi người chơi
            foreach ($users as $key => $user) {
                $user['rank'] = $key + 1;
                $users[$key] = $user;
            }
    
            $data = [
                'status' => 200,
                'message' => 'Users Fetched Successfully',
                'data' => $users
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No Users Found',
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

?>