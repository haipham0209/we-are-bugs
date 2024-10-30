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

// 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // データ受け取り
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $email = $conn->real_escape_string($_POST['email']);
    $sname = $conn->real_escape_string($_POST['sname']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    // username 存在かどうか
    $check_sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 存在場合
        header("Location: ../StoreRegister.php?error=username_exists&username=" . urlencode($username). "&sname=" . urlencode($sname) . "&email=" . urlencode($email));

        exit();
    }
    // sname 存在かどうか
    $check_sname_sql = "SELECT * FROM store WHERE sname = ?";
    $stmt = $conn->prepare($check_sname_sql);
    $stmt->bind_param("s", $sname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 存在場合 - Nếu sname đã tồn tại
        header("Location: ../StoreRegister.php?error=sname_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    // email 存在かどうか
    $check_email_sql = "SELECT * FROM user WHERE mail = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 存在場合
        // header("Location: ../StoreRegister.php?error=email_exists");
        header("Location: ../StoreRegister.php?error=email_exists&username=" . urlencode($username). "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    //password 2 time checl
    if (strlen($password) < 6) {
        // header('Location: ?error=password_short');
        header("Location: ../StoreRegister.php?error=password_short&username=" . urlencode($username) . "&sname=" . urlencode($sname). "&email=" . urlencode($email));
        exit();
    }

    // Kiểm tra xem hai mật khẩu có khớp không
    if ($password !== $confirm_password) {
        // header('Location: ?error=password_mismatch');
        header("Location: ../StoreRegister.php?error=password_mismatch&username=" . urlencode($username) . "&sname=" . urlencode($sname). "&email=" . urlencode($email));
        exit();
    }
// ---------------------------------------- INSERT -----------------------------------------------------------
        $sql = "INSERT INTO user (username, password, mail) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $username, $password, $email);
        if ($stmt->execute()) {
            // FLAG　保存する
            $userid = $stmt->insert_id; // Lấy ID của bản ghi mới
                //inssert store
            $insert_store_sql = "INSERT INTO store (sname, userid) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_store_sql);
            $stmt->bind_param("si", $sname, $userid); // Giả định `owner` là username
            if (!$stmt->execute()) {
            echo "insert store ERROR";
            exit();
            }
            header("Location: ../StoreLogin.php?username=" . urlencode($username));
            // echo "<script>
            //         localStorage.setItem('registerSuccess', 'true');
            //         window.location.href = '../StoreRegister.php';
            //         </script>";
            exit();
        } else {
            echo "insert ERROR";
        }
    

// ------------------------------------end insert---------------------------------------
    $stmt->close();
    $conn->close();
} else {
    // POST ではない
    echo "POST　ではない";
}

// function generateToken() {
//     return bin2hex(random_bytes(16));
// }
?>
