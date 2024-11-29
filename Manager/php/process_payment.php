<?php
// Nhận dữ liệu JSON từ AJAX
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra và xử lý dữ liệu từ AJAX
if (isset($data['complete']) && $data['complete'] === true) {
    $total_price = $data['total_price'];
    $received_amount = $data['received_amount'];
    $cart_items = $data['cart'];  // Dữ liệu giỏ hàng

    // Kiểm tra nếu giỏ hàng trống
    if (empty($cart_items)) {
        echo json_encode(['error' => 'Giỏ hàng không có sản phẩm.']);
        exit;
    }

    // Thực hiện thanh toán, lưu đơn hàng vào database
    // (Cần thực hiện kết nối cơ sở dữ liệu trước)
    // Thực hiện lưu vào bảng orders
    $conn = new mysqli("localhost", "root", "", "your_database_name");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO orders (total_price, received_amount, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("dd", $total_price, $received_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Lấy ID của đơn hàng vừa được tạo

    // Thêm các chi tiết đơn hàng vào bảng order_details
    foreach ($cart_items as $item) {
        $productid = $item['barcode'];  // Mã sản phẩm
        $quantity = $item['quantity'];  // Số lượng
        $price = $item['price'];        // Giá sản phẩm

        // Thêm chi tiết vào bảng order_details
        $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $order_id, $productid, $quantity, $price);
        $stmt->execute();

        // Cập nhật tồn kho sản phẩm (nếu cần)
        $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $stmt->bind_param("ii", $quantity, $productid);
        $stmt->execute();
    }

    $conn->close();

    // Trả về kết quả cho AJAX
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['error' => 'Dữ liệu không hợp lệ.']);
}
?>
