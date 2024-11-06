<?php
session_start();
include(__DIR__ . '/db_connect.php'); // データベース接続ファイルをインクルード

// ユーザーがログインしているか確認
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

// パスワードが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    // データベースからユーザーのパスワードを取得
    $stmt = $conn->prepare("SELECT password FROM user WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // 入力されたパスワードが正しいかをチェック
    if (password_verify($password, $hashedPassword)) {

        // storeテーブルの関連レコードを削除
        $deleteStoreStmt = $conn->prepare("DELETE FROM store WHERE userid = ?");
        $deleteStoreStmt->bind_param("i", $userid);
        $deleteStoreStmt->execute();
        $deleteStoreStmt->close();

        // パスワードが正しければアカウントを削除
        $stmt = $conn->prepare("DELETE FROM user WHERE userid = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $stmt->close();

        // セッションをクリアしてログインページへリダイレクト
        session_destroy();
        header("Location: ../StoreLogin.php");
        exit;
    } else {
        header("Location: ../profileEdit.php?error=invalid_password");
        exit;
    }
}
