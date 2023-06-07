<?php

require '../inc/dbcon.php';
//error422
function error422($message)
{

    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}
//CREATE USERS
function storeUser($userInput)
{
    global $conn;

    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);

    if (empty(trim($username))) {
        return error422('Enter your username');
    } elseif (empty(trim($password))) {
        return error422('Enter your password');
    } elseif (strlen($password) < 6) {
        return error422('Password must be at least 6 characters long and contain a special character');
    } else {
        $username_query = "SELECT * FROM users WHERE username = '$username'";
        $username_query_run = mysqli_query($conn, $username_query);

        if (mysqli_num_rows($username_query_run) > 0) {
            $data = [
                'status' => 400,
                'message' => 'This username is already taken',
            ];
            header("HTTP/1.0 400 Bad Request");
            return json_encode($data);
        } else {
            $query = "INSERT INTO 'users' ('id', 'username', 'password', 'coin') VALUES (NULL, '$username', '$password', '0')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                $data = [
                    'status' => 201,
                    'message' => 'User Created Successfully',
                ];
                header("HTTP/1.0 201 Created");
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
}


//READ USERS
function getUserList()
{

    global $conn;

    $query = "SELECT * FROM users";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        if (mysqli_num_rows($query_run) > 0) {

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'User List Fetched Successfully',
                'data' => $res,
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No User Found',
            ];
            header("HTTP/1.0 404 No User Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

//GET USER WITH ID

function getUser($userParams) {
    // Kết nối đến cơ sở dữ liệu
    global $conn;

    if($userParams['id'] == null){
        return error422('Enter your id');
    }

    $idUser = mysqli_real_escape_string($conn, $userParams['id']);
    // Truy vấn để lấy thông tin người dùng theo id
    $sql = "SELECT id, username, coin FROM users WHERE id = $idUser" ;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = array();
    
        // Lấy thông tin người dùng
        while ($row = $result->fetch_assoc()) {
            $user["id"] = $row["id"];
            $user["username"] = $row["username"];
            $user["coin"] = $row["coin"];
        }
    
        // Truy vấn để lấy thông tin điểm số của người dùng
        $scoresSql = "SELECT songs.name, scores.score, scores.star
                      FROM scores
                      INNER JOIN songs ON scores.id_song = songs.id
                      WHERE scores.id_user = $idUser";
        $scoresResult = $conn->query($scoresSql);
    
        if ($scoresResult->num_rows > 0) {
            $user["scores"] = array();
    
            // Lấy thông tin điểm số
            while ($scoresRow = $scoresResult->fetch_assoc()) {
                $score = array(
                    "name" => $scoresRow["name"],
                    "score" => $scoresRow["score"],
                    "star" => $scoresRow["star"]
                );
    
                // Thêm thông tin điểm số vào mảng users
                $user["scores"][] = $score;
            }
        } else {
            $user["scores"] = array();
        }
    
        // Trả về kết quả dưới dạng JSON
        return $user;
    } else {
        return array('error' => 'User not found');
    }
}





//UPDATE USERS
function updateUser($userInput, $userParams)
{

    global $conn;

    if (!isset($userParams['id'])) {

        return error422('user id not found in URL');

    } elseif ($userParams['id'] == null) {

        return error422('Enter the user id');
    }

    $userId = mysqli_real_escape_string($conn, $userParams['id']);

    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);

    if (empty(trim($username))) {

        return error422('Enter your username');
    } elseif (empty(trim($password))) {

        return error422('Enter your password');
    } else {

        $query = "UPDATE users SET username='$username', password='$password' WHERE id='$userId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'message' => 'User Updated Successfully',
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


//DELETE USERS
function deleteUser($userParams)
{

    global $conn;

    if (!isset($userParams['id'])) {

        return error422('user id not found in URL');

    } elseif ($userParams['id'] == null) {

        return error422('Enter the user id');
    }

    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    $query = "DELETE FROM users WHERE id='$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            'status' => 200,
            'message' => 'User Deleted Successfully',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {

        $data = [
            'status' => 404,
            'message' => 'User Not found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}



function getUserWithUsernamePassword($userParams)
{

    global $conn;
    if ($userParams['username'] == null) {

        return error422('Enter your username');
    }
    if ($userParams['password'] == null) {

        return error422('Enter your password');
    }
    $username = mysqli_real_escape_string($conn, $userParams['username']);
    $password = mysqli_real_escape_string($conn, $userParams['password']);
    $query = "SELECT * FROM users WHERE username='$username'  AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {

            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'User Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No User Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }

    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

?>