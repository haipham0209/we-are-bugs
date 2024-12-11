<?php
include('auth_check.php');
include('db_connect.php');



// Kết nối tới cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "ssssssssss";
// Lấy danh sách đơn hàng cho ngày đã chọn
if (isset($_POST['selected_date'])) {
    echo"333333333333333";
    $selectedDate = $_POST['selected_date'];
    $storeId = $_COOKIE['store_id']; // Lấy store_id từ cookie

    // Truy vấn danh sách đơn hàng
    $sql = "SELECT o.order_number, SUM(od.quantity) as total_quantity, SUM(od.quantity * p.price) as total_price
            FROM orders o
            JOIN order_details od ON o.order_number = od.order_number
            JOIN product p ON od.productid = p.productid
            WHERE o.store_id = ? AND DATE(o.order_date) = ?
            GROUP BY o.order_number";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $storeId, $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode($orders);
    exit();
}
// echo "ssssssssss";
// Lấy chi tiết đơn hàng khi người dùng yêu cầu
if (isset($_POST['order_number'])) {
    $orderNumber = $_POST['order_number'];
// echo "ssssssssss";
// echo $orderNumber;
    // Truy vấn chi tiết đơn hàng
    $payment_method = "現金"; // mặc định thanh toán bằng tiền mặt, vì chưa có cột hình thức thanh toán
    $sql = "SELECT p.pname, od.quantity, p.price, o.received_amount
            FROM order_details od
            JOIN product p ON od.productid = p.productid
            JOIN orders o ON od.order_number = o.order_number
            WHERE od.order_number = ?";
            // echo "ssssssssss";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderNumber);
    $stmt->execute();
    $result = $stmt->get_result();
// echo "ssssssssss";
    $orderDetails = [];
    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = $row;
    }

    // Trả về dữ liệu chi tiết đơn hàng dưới dạng JSON
    // echo "ssssssssss";
    echo json_encode($orderDetails);
    exit();
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
