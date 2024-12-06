<?php
include('auth_check.php');
include('db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

// Khởi tạo giao dịch
$conn->set_charset("utf8");

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
        echo json_encode(['success' => false, 'error' => 'エラー発生しました。']);
        exit;
    }

    if ($total_price <= 0 || $received_amount < $total_price) {
        echo json_encode(['success' => false, 'error' => '金額不足している']);
        exit;
    }

    try {
        // Bắt đầu giao dịch
        $conn->begin_transaction();

        $order_number = $_SESSION['order_number'];
        // echo $order_number;
        // Lưu đơn hàng vào bảng orders
        $stmt = $conn->prepare("INSERT INTO orders (order_number, store_id, customer_id, total_price, status, received_amount) VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->bind_param("sddsd",$order_number, $store_id, $customer_id, $total_price, $received_amount);
        $stmt->execute();
        // $order_id = $stmt->insert_id;

        // Lưu chi tiết đơn hàng vào bảng order_details
        foreach ($products as $item) {
            $barcode = $item['barcode'];  // Lấy barcode từ giỏ hàng
            $quantity = $item['quantity']; // Số lượng sản phẩm

            // Truy vấn productid dựa vào barcode và storeid
            $stmt = $conn->prepare("SELECT productid FROM product WHERE barcode = ? AND storeid = ?");
            $stmt->bind_param("si", $barcode, $store_id);
            $stmt->execute();
            $stmt->bind_result($productid);
            $stmt->fetch();
            
            // Giải phóng kết quả truy vấn
            $stmt->free_result();  // Giải phóng kết quả truy vấn SELECT

            // Kiểm tra nếu productid tồn tại
            if ($productid) {
                // Thêm chi tiết vào bảng order_details
                $stmt = $conn->prepare("INSERT INTO order_details (order_number, productid, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("sii", $order_number, $productid, $quantity);
                $stmt->execute();
                

                // Cập nhật tồn kho sản phẩm
                $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE productid = ?");
                $stmt->bind_param("ii", $quantity, $productid);
                $stmt->execute();
            } else {
                // Nếu không tìm thấy productid, trả về lỗi
                echo json_encode(['success' => false, 'error' => '商品存在しない']);
                $conn->rollback();
                exit;
            }
        }

        // Commit giao dịch nếu tất cả lệnh thành công
        $conn->commit();
        echo json_encode(['success' => true, 'order_number' => $order_number]);

    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
?>
