<?php
// データベース接続
include('db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("データベース接続エラー");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "パスワードが一致しません。";
        echo '<p><a href="../ResetPassword.php?username=' . urlencode($username) . '">戻る</a></p>';
        exit();
    }

    // 新しいパスワードをハッシュ化
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // パスワードを更新
    $update_sql = "UPDATE user SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ss", $hashed_password, $username);
    if ($stmt->execute()) {
        echo "パスワードが正常にリセットされました。";
        echo '<p><a href="../StoreLogin.php">ログイン画面に戻る</a></p>';
    } else {
        echo "パスワードの更新に失敗しました。";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "不正なリクエストです。";
}
