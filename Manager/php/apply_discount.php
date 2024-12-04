<?php
// 必要なファイルを読み込む
include('./auth_check.php');
include('./db_connect.php');

// JSON形式でデータを受信
$requestData = json_decode(file_get_contents('php://input'), true);

// デバッグ用ログ出力
file_put_contents('debug_log.txt', print_r($requestData, true));

// データを取得
$productId = $requestData['productId'] ?? null;
$discountRate = $requestData['discountRate'] ?? null;
$discountedPrice = $requestData['discountedPrice'] ?? null;

// 入力データのバリデーション
if (!$productId || !is_numeric($productId) || !$discountRate || !is_numeric($discountRate) || !$discountedPrice || !is_numeric($discountedPrice)) {
    echo json_encode(['success' => false, 'message' => '無効な入力データ']);
    exit;
}

// データベースに接続し割引を適用
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'データベース接続エラー']);
    exit;
}

$sql = "UPDATE product SET discounted_price = ? WHERE productid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $discountedPrice, $productId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '割引が適用されました']);
} else {
    echo json_encode(['success' => false, 'message' => '割引の適用に失敗しました']);
}

$stmt->close();
$conn->close();
