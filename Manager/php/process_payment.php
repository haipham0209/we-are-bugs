
<?php
    include('auth_check.php');
    include('db_connect.php');
// header('Content-Type: application/json');
// echo json_encode(['success' => true, 'order_id' => $order_id]);
$customer_id = null;  // Lấy customer_id từ session, nếu có
$store_id = $_SESSION['storeid'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu JSON từ php://input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Lấy dữ liệu từ JSON
    $total_price = $data['total_price'] ?? 0;
    $received_amount = $data['received_amount'] ?? 0;
    $products = $data['products'] ?? [];
    
    // Kiểm tra dữ liệu nhận được
    if (empty($products)) {
        echo json_encode(['success' => false, 'error' => 'Giỏ hàng trống.']);
        // echo $products;
        exit;
    }

    if ($total_price <= 0 || $received_amount < $total_price) {
        echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ hoặc số tiền nhận được không đủ.']);
        exit;
    }

    // Kết nối cơ sở dữ liệu


    // Đặt mã hóa UTF-8 để tránh lỗi ký tự đặc biệt
    $conn->set_charset("utf8");


    try {
        // Lưu đơn hàng vào bảng orders
        $stmt = $conn->prepare("INSERT INTO orders (store_id, customer_id, total_price, status, received_amount) VALUES (?, ?, ?, 'pending', ?)");
        $stmt->bind_param("ddsd", $store_id, $customer_id, $total_price, $received_amount);
        $stmt->execute();
        $order_id = $stmt->insert_id;
    
        // Lưu chi tiết đơn hàng vào bảng order_details
        foreach ($products as $item) {
            $barcode = $item['barcode'];  // Lấy barcode từ giỏ hàng
            $quantity = $item['quantity']; // Số lượng sản phẩm
            $price = $item['price']; // Giá sản phẩm (không sử dụng trong bảng order_details nhưng có thể cần dùng)
    
            // Truy vấn productid dựa vào barcode và storeid
            $stmt = $conn->prepare("SELECT productid FROM product WHERE barcode = ? AND storeid = ?");
            $stmt->bind_param("si", $barcode, $store_id);
            $stmt->execute();
            $stmt->bind_result($productid);
            $stmt->fetch();
    
            // Kiểm tra nếu productid tồn tại
            if ($productid) {
                // Thêm chi tiết vào bảng order_details
                $stmt = $conn->prepare("INSERT INTO order_details (orderid, productid, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $order_id, $productid, $quantity);
                $stmt->execute();
    
                // Cập nhật tồn kho sản phẩm
                $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE productid = ?");
                $stmt->bind_param("ii", $quantity, $productid);
                $stmt->execute();
            } else {
                // Nếu không tìm thấy productid, trả về lỗi
                echo json_encode(['success' => false, 'error' => 'Sản phẩm không tồn tại trong cửa hàng này.']);
                exit;
            }
        }
    
        // Commit giao dịch
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        // echo "Lỗi trong quá trình xử lý thanh toán: " . $e->getMessage();
        // echo json_encode(['success' => false, 'error' =>" s" ]);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);

    } finally {
        $conn->close();
    }
}
?>
