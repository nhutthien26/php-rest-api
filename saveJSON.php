<?php

require 'inc/dbcon.php';
function saveJSONToDatabase($json) {
    global $conn;

    // Phân tích JSON
    $data = json_decode($json, true);

    // Lưu vào bảng 'users'
    foreach ($data['users'] as $user) {
        $user_id = $user['id'];
        $user_username = $user['username'];
        $user_coin = $user['coin'];

        $sql_users = "INSERT INTO users (id, username, password, coin) VALUES ('$user_id', '$user_username', '$user_coin')";

        if ($conn->query($sql_users) !== TRUE) {
            echo "Lỗi: " . $sql_users . "<br>" . $conn->error;
            return;
        }
    }

    // Lưu vào bảng 'score'
    foreach ($data['scores'] as $score) {
        $score_id_user = $score['id_user'];
        $score_id_song = $score['id_song'];
        $score_score = $score['score'];
        $score_star = $score['star'];

        $sql_scores = "INSERT INTO score (id_user, id_song, score, star) VALUES ('$score_id_user', '$score_id_song', '$score_score', '$score_star')";

        if ($conn->query($sql_scores) !== TRUE) {
            echo "Lỗi: " . $sql_scores . "<br>" . $conn->error;
            return;
        }
    }

    // Lưu vào bảng 'songs'
    foreach ($data['songs'] as $song) {
        $song_id = $song['id'];
        $song_name = $song['name'];
        $song_data = $song['data'];
        $song_image = $song['image'];
        $song_mp3 = $song['mp3'];

        $sql_songs = "INSERT INTO songs (id, name, data, image, mp3) VALUES ('$song_id', '$song_name', '$song_data', '$song_image', '$song_mp3')";

        if ($conn->query($sql_songs) !== TRUE) {
            echo "Lỗi: " . $sql_songs . "<br>" . $conn->error;
            return;
        }
    }

    // Đóng kết nối cơ sở dữ liệu
    $conn->close();

    echo "Dữ liệu đã được lưu vào cơ sở dữ liệu.";
}

// Nhận JSON từ client và gọi hàm lưu vào cơ sở dữ liệu
$json_data = file_get_contents('php://input');
saveJSONToDatabase($json_data);
?>