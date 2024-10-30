<?php
include('./php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "KHÔNG THỂ KẾT NỐI MÁY CHỦ";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $email = $conn->real_escape_string($_POST['email']);
    $sname = $conn->real_escape_string($_POST['sname']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Kiểm tra username có tồn tại không
    $check_sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../StoreRegister.php?error=username_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    // Kiểm tra sname có tồn tại không
    $check_sname_sql = "SELECT * FROM store WHERE sname = ?";
    $stmt = $conn->prepare($check_sname_sql);
    $stmt->bind_param("s", $sname);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../StoreRegister.php?error=sname_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    // Kiểm tra email có tồn tại không
    $check_email_sql = "SELECT * FROM user WHERE mail = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: ../StoreRegister.php?error=email_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    // Kiểm tra độ dài mật khẩu
    if (strlen($password) < 6) {
        header("Location: ../StoreRegister.php?error=password_short&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        header("Location: ../StoreRegister.php?error=password_mismatch&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }

    // Sinh token ngẫu nhiên
    $token = bin2hex(random_bytes(16));
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Chèn dữ liệu vào bảng user
    $sql = "INSERT INTO user (username, password, mail, token) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $token);

    if ($stmt->execute()) {
        $userid = $stmt->insert_id;

        // Chèn dữ liệu vào bảng store
        $insert_store_sql = "INSERT INTO store (sname, userid) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_store_sql);
        $stmt->bind_param("si", $sname, $userid);
        $stmt->execute();

        // Gửi email xác thực
        $verify_link = "http://yourdomain.com/verify.php?token=$token";
        $subject = "Xác thực tài khoản của bạn";
        $message = "Chào $username,\n\nVui lòng bấm vào liên kết sau để kích hoạt tài khoản của bạn:\n$verify_link";
        $headers = "From: no-reply@yourdomain.com\r\n" .
                   "Content-Type: text/plain; charset=UTF-8";

        if (mail($email, $subject, $message, $headers)) {
            header("Location: ../mail_check.html");
            exit();
        } else {
            echo "Lỗi khi gửi email.";
        }
    } else {
        echo "Lỗi khi tạo tài khoản.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
