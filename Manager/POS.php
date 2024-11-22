<?php
include('./php/auth_check.php'); // Kiểm tra quyền người dùng
include('./php/db_connect.php'); // Kết nối cơ sở dữ liệu

// Khởi tạo kết nối cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Khởi tạo biến sản phẩm
$product = null;
$totalPrice = 0;
$totalQuantity = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    $stmt = $conn->prepare("SELECT * FROM product WHERE barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // echo json_encode($products);  // Return the list of products
}
// Xử lý giỏ hàng và tính toán tổng giá trị
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
        $totalQuantity += $item['quantity'];
    }
}

// Hàm tạo mã khách hàng
function generateCustomerCode($conn, $storeid) {
    $today = date('Y-m-d');
    $month = date('m');
    $day = date('d');

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM order_history WHERE storeid = ? AND order_date = ?");
    $stmt->bind_param("is", $storeid, $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $order_number = str_pad($result['count'] + 1, 3, "0", STR_PAD_LEFT);
    return $month . $day . $order_number;
}

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete'])) {
    $storeid = $_SESSION['storeid'];
    $products = $_SESSION['cart'];
    $total_price = $_POST['total_price'];
    $received_amount = $_POST['received_amount'];
    $customer_code = generateCustomerCode($conn, $storeid);

    // Thêm đơn hàng vào order_history
    $stmt = $conn->prepare("INSERT INTO order_history (customer_code, storeid, total_price, order_date) VALUES (?, ?, ?, CURDATE())");
    $stmt->bind_param("sid", $customer_code, $storeid, $total_price);
    if (!$stmt->execute()) {
        echo "Lỗi: " . $stmt->error;
    }
    $orderid = $conn->insert_id;

    // Thêm chi tiết đơn hàng vào order_details
    foreach ($products as $product) {
        $stmt = $conn->prepare("INSERT INTO order_details (orderid, productid, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $orderid, $product['productid'], $product['quantity']);
        if (!$stmt->execute()) {
            echo "Lỗi: " . $stmt->error;
        }

        // Cập nhật số lượng tồn kho
        $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE productid = ?");
        $stmt->bind_param("ii", $product['quantity'], $product['productid']);
        if (!$stmt->execute()) {
            echo "Lỗi: " . $stmt->error;
        }
    }

    echo json_encode(['success' => true, 'order_id' => $customer_code]);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/POS.css">
    <script src="./scripts/camera.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="./scripts/POS.js"></script>
    <title>POS</title>
</head>
<body>
<header>
    <div class="main-navbar">
        <div class="search-scan">
            <form method="POST">
                <input type="text" name="barcode" id="barcode-input" class="search-bar" placeholder="Search...">
            </form>
            <div id="barcode-suggestions" class="suggestions-list" style="display:none;"></div>
            <img src="./images/camera-icon.png" class="camera-icon" onclick="toggleCamera()">
        </div>
        <script>
                let isCameraRunning = false; // カメラの状態を管理

                function toggleCamera() {
                    if (isCameraRunning) {
                        stopScanner();
                        isCameraRunning = false;
                    } else {
                        startScanner();
                        isCameraRunning = true;
                    }
                }
            </script>
        <button class="main-home">
            <h1 class="logo">WRB</h1>
        </button>
    </div>
</header>
<main>
    <div id="camera" style="display: none;">
        <button id="stopBtn" onclick="toggleCamera()">カメラ停止</button>
    </div>
    <div class="pos">
        <h2>会計</h2>
        <div class="id-time">
            <p id="customer-id">注文番号: <?php echo generateCustomerCode($conn, $_SESSION['storeid']); ?></p>
            <div id="datetime">
                <p id="date"></p>
                <p id="time"></p>
            </div>
        </div>
        <table id="product-table">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th>数量</th>
                    <th>価格</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['pname']); ?></td>
                    <td><input type="number" value="1" min="1" class="product-quantity"></td>
                    <td><?php echo htmlspecialchars($product['price']); ?>¥</td>
                </tr>
                <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: gray;">商品が見つかりません</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div id="empty-message" style="text-align: center; color: gray; display: none;">
            <p>商品が追加されていません。</p>
        </div>
        <form id="payment-form">
            <div class="pay">
                <p>支払方法:
                    <select id="payment-method">
                        <option value="cash">現金</option>
                        <option value="credit">クレジットカード</option>
                    </select>
                </p>
            </div>
            <div class="total">
                <p>合計: <span id="total-price"><?php echo number_format($totalPrice, 2); ?>¥</span></p>
                <p>(税込10%)</p>
                <p>割引き: 
                    <input type="number" id="waribiki-input" value="0" min="0" max="100" onchange="updateTotal()"> %
                </p>
                <label for="received-amount">お預かり:</label>
                <input type="number" id="received-amount" oninput="calculateChange()">
                <p>お釣り: <span id="change-amount">0¥</span></p>
                <p>数量: <span id="total-quantity"><?php echo $totalQuantity; ?></span></p>
            </div>
        </form>
        <form method="POST">
            <input type="hidden" name="total_price" id="hidden-total-price" value="0">
            <input type="hidden" name="received_amount" id="hidden-received-amount" value="0">
            <button type="submit" name="complete" class="button-pay">完了</button>
        </form>
    </div>
</main>
</body>
</html>
