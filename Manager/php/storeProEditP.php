<?php
// Kết nối cơ sở dữ liệu
include('db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = (int) $_POST['userid']; // Lấy userid từ form
    // $sname = $conn->real_escape_string($_POST['sname']);
    $address = $conn->real_escape_string($_POST['address']);
    $tel = $conn->real_escape_string($_POST['phone']);

    ////////////////////////////////////////////
    // Kiểm tra xem userid có hợp lệ không
    $check_user_sql = "SELECT * FROM user WHERE userid = ?";
    $stmt = $conn->prepare($check_user_sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../error.php?uname_not_exist");
        exit();
    }

    // Kiểm tra xem có bản ghi nào trong bảng store không
    $check_store_sql = "SELECT * FROM store WHERE userid = ?";
    $stmt = $conn->prepare($check_store_sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu có, thực hiện UPDATE
        $update_sql = "UPDATE store SET address = ?, tel = ? WHERE userid = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $address, $tel, $userid);

        if ($stmt->execute()) {
            // echo "Cập nhật thành công";
            header("Location: ../main.php");
        } else {
            header("Location: ../error.php?updateerror");
        }
    } else {
        // Nếu không có, thực hiện INSERT
        $insert_sql = "INSERT INTO store (userid, address, tel) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iss", $userid, $address, $tel);

        if ($stmt->execute()) {
            // echo "Thêm thành công";
            header("Location: ../main.php");
        } else {
            header("Location: ../error.php?storeProEditP");
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../error.php?notPost");
}
?>
