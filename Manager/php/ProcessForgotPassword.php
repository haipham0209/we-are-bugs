<?php
// データベース接続
$servername = "localhost";
$username = "dbuser";
$password = "ecc";
$dbname = "wearebugs";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // パスワード一致確認
    if ($new_password !== $confirm_password) {
        echo "パスワードが一致しません。<a href='../ForgotPassword.php'>戻る</a>";
        exit();
    }

    // ユーザー名とメールの一致を確認
    $check_sql = "SELECT * FROM user WHERE username = ? AND mail = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // パスワードをハッシュ化して更新
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE user SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ss", $hashed_password, $username);
        $stmt->execute();

        echo "パスワードがリセットされました。<a href='../StoreLogin.php'>ログイン画面に戻る</a>";
    } else {
        echo "ユーザー名またはメールアドレスが正しくありません。<a href='../ForgotPassword.php'>戻る</a>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "不正なリクエストです。";
}
