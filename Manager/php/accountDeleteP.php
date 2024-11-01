<?php
session_start();
include(__DIR__ . '/db_connect.php'); // データベース接続ファイルをインクルード

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];

    // storeテーブルの関連レコードを削除
    $deleteStoreStmt = $conn->prepare("DELETE FROM store WHERE userid = ?");
    $deleteStoreStmt->bind_param("i", $userid);
    $deleteStoreStmt->execute();
    $deleteStoreStmt->close();

    // userテーブルのレコードを削除
    $deleteUserStmt = $conn->prepare("DELETE FROM user WHERE userid = ?");
    $deleteUserStmt->bind_param("i", $userid);
    $deleteUserStmt->execute();
    $deleteUserStmt->close();

    $conn->close();

    session_destroy();
    header("Location: ../StoreLogin.php");
    exit;
} else {
    echo "ユーザーが見つかりません。";
}
