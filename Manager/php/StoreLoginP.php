<?php

// connect
$servername = "localhost";
$username = "dbuser";
$password = "ecc";
$dbname = "wearebugs";

// $servername = "localhost";
// $username = "se2a_24_bugs";
// $password = "X@7zERHL";
// $dbname = "se2a_24_bugs";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // データ受け取り
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // ユーザー名存在かどうか
    $check_sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // 存在しない
    if ($result->num_rows === 0) {
        header("Location: ../StoreLogin.php?error=username_not_found&username=" . urlencode($username));
        exit();
    } else {
        // 存在場合
        $user = $result->fetch_assoc();
        //user data 取得
        $stored_password = $user['password']; 
        $userid = $user["userid"];
        $mail = $user["mail"]; // Lấy địa chỉ email từ cơ sở dữ liệu


        // password check
        if ($password === $stored_password) {
            //TOKEN　発生
            $token = bin2hex(random_bytes(16)); 
             // DBに保存
             $update_token_sql = "UPDATE user SET token = ? WHERE userid = ?";
             $stmt = $conn->prepare($update_token_sql);
             $stmt->bind_param("si", $token, $userid);
             $stmt->execute();

                     // Truy vấn thông tin cửa hàng dựa trên userid
            $store_sql = "SELECT * FROM store WHERE userid = ?";
            $stmt = $conn->prepare($store_sql);
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            $store_result = $stmt->get_result();
        // Lấy thông tin cửa hàng
        if ($store_result->num_rows > 0) {
            $store = $store_result->fetch_assoc();
            $storeid = $store["storeid"];
            // Lưu thông tin cửa hàng vào session
            $_SESSION['storeid'] = $storeid; // Lưu storeid
        }    
            // session 開始
            session_destroy();
            session_start();
            setcookie('username', $username, time() + (864000 * 30), "/"); //30day
            setcookie('userid', $userid, time() + (864000 * 30), "/"); //30day
            setcookie('token', $token, time() + (8640000 * 30), "/");
            setcookie('loggedin', true, time() + (8640000 * 30), "/");

            $_SESSION['username'] = $username; // Lưu ID người dùng
            $_SESSION['userid'] = $userid; // Lưu ID người dùng
            $_SESSION['mail'] = $mail; // Lưu địa chỉ email
            $_SESSION['storeid'] = $storeid; // Lưu storeid
            $_SESSION['last_activity'] = time(); // Lưu thời gian hoạt động cuối cùng
            //page 移動
            header("Location: ../main.php");
            exit();
        } else {
            // パスワード違います
            header("Location: ../StoreLogin.php?error=incorrect_password&username=" . urlencode($username));
            exit();
        }
    }

    $stmt->close();
    $conn->close();
} else {
    // POSTではない
    echo "POSTではない";
}
?>
