<?php
// データベース接続
include('db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("データベース接続エラー");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    // ユーザー名とメールアドレスの確認
    $check_sql = "SELECT * FROM user WHERE username = ? AND mail = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // ユーザーが確認できた場合、パスワード設定画面へ遷移
        header("Location: ../ResetPassword.php?username=" . urlencode($username));
        exit();
    } else {
        echo "ユーザー名またはメールアドレスが一致しません。";
        echo '<p><a href="../ForgotPassword.php">戻る</a></p>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo "不正なリクエストです。";
}
