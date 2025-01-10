<?php
require 'db_connect.php';

$order_number = $_GET['order_number'];

// 注文の詳細データを取得
$sql = "
    SELECT 
        p.pname, 
        od.quantity, 
        p.price, 
        (od.quantity * p.price) AS total_price 
    FROM order_details od
    JOIN product p ON od.productid = p.productid
    WHERE od.order_number = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $order_number);
$stmt->execute();
$result = $stmt->get_result();

$orderDetails = [];
while ($row = $result->fetch_assoc()) {
    $orderDetails[] = $row;
}

echo json_encode($orderDetails);

$stmt->close();
$conn->close();
