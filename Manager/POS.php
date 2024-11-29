<?php
include('./php/auth_check.php'); // Kiểm tra quyền người dùng
include('./php/db_connect.php'); // Kết nối cơ sở dữ liệu
include('./php/POS_product.php');

// Khởi tạo kết nối cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Khởi tạo giỏ hàng và biến cần thiết
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$totalPrice = 0;
$totalQuantity = 0;

// Xử lý thêm sản phẩm vào giỏ hàng qua barcode
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];
    $storeid = $_SESSION['storeid']; 

    // Lấy thông tin sản phẩm
    $product = getProductByBarcode($conn, $barcode, $storeid);

    if ($product) {
        // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng hay chưa
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['barcode'] === $barcode) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }
        if (!$found) {
            // Thêm sản phẩm mới vào giỏ hàng
            $_SESSION['cart'][] = [
                'pname' => $product['pname'],
                'price' => $product['price'],
                'quantity' => 1,
                'barcode' => $barcode
            ];
        }
    } else {
        $error_message = "Sản phẩm không tìm thấy!";
    }
}

// Tính toán tổng giá trị giỏ hàng
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
    $totalQuantity += $item['quantity'];
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
    $customer_code = generateCustomerCode($conn, $storeid);
    $products = $_SESSION['cart'];
    $total_price = $_POST['total_price'];
    $received_amount = $_POST['received_amount'];
   
    // Thêm đơn hàng vào order_history
    $stmt = $conn->prepare("INSERT INTO order_history (customer_code, storeid, total_price, order_date) VALUES (?, ?, ?, CURDATE())");
    $stmt->bind_param("sid", $customer_code, $storeid, $total_price);
    if (!$stmt->execute()) {
        echo json_encode(['error' => $stmt->error]);
        exit;
    }
    $orderid = $conn->insert_id;

    // Thêm chi tiết đơn hàng vào order_details
    foreach ($products as $product) {
         // Kiểm tra tồn kho trước khi thực hiện cập nhật
         $stmt = $conn->prepare("SELECT stock_quantity FROM product WHERE productid = ?");
         $stmt->bind_param("i", $product['productid']);
         $stmt->execute();
         $result = $stmt->get_result()->fetch_assoc();
 
         if ($result['stock_quantity'] < $product['quantity']) {
             echo json_encode(['error' => 'Số lượng tồn kho không đủ cho sản phẩm: ' . $product['pname']]);
             exit;
         }
         // Chèn dữ liệu vào chi tiết đơn hàng
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

    $_SESSION['cart'] = []; // Xóa giỏ hàng sau khi thanh toán
    echo json_encode(['success' => true, 'order_id' => $customer_code]);
    exit;
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
        <div id="suggestionList"></div>
        <script>
// Định nghĩa các phần tử
const searchBox = document.getElementById('barcode-input'); // Ô nhập liệu
const suggestionList = document.getElementById('barcode-suggestions'); // Danh sách gợi ý

// Lắng nghe sự kiện input
searchBox.addEventListener('input', function () {
    const keyword = searchBox.value.trim();
    if (keyword.length > 0) {
        // Gửi yêu cầu đến PHP API
        fetch('./php/search_product.php?keyword=' + encodeURIComponent(keyword))
            .then(response => response.json())
            .then(data => {
                // Xóa gợi ý cũ
                suggestionList.innerHTML = '';
                suggestionList.style.display = 'block'; // Hiển thị danh sách

                // Duyệt danh sách sản phẩm trả về
                data.forEach(product => {
    const div = document.createElement('div');
    div.className = 'suggestion-item'; // Thêm class để tiện style

    // Tạo phần tử div cho tên sản phẩm
    const nameDiv = document.createElement('div');
    nameDiv.textContent = `${product.pname}`;
    div.appendChild(nameDiv);

    // Tạo phần tử img cho ảnh sản phẩm
    const img = document.createElement('img');
    img.src = product.productImage; // Giả sử trường productImage chứa đường dẫn đến ảnh sản phẩm
    img.alt = product.pname;
    img.style.width = '50px'; // Đặt kích thước ảnh (bạn có thể thay đổi theo nhu cầu)
    img.style.marginLeft = '10px'; // Khoảng cách giữa tên sản phẩm và ảnh
    div.appendChild(img);

    div.dataset.id = product.productid; // Lưu ID sản phẩm

    div.addEventListener('click', () => {
        // Khi chọn sản phẩm, gán vào ô input
        searchBox.value = `${product.pname}`;
        suggestionList.innerHTML = ''; // Xóa danh sách gợi ý
        suggestionList.style.display = 'none'; // Ẩn danh sách
    });

    suggestionList.appendChild(div);
});

            })
            .catch(error => console.error('Error:', error));
    } else {
        // Xóa danh sách nếu từ khóa trống
        suggestionList.innerHTML = '';
        suggestionList.style.display = 'none';
    }
});

// Ẩn danh sách khi click ra ngoài
document.addEventListener('click', function (e) {
    if (!suggestionList.contains(e.target) && e.target !== searchBox) {
        suggestionList.innerHTML = '';
        suggestionList.style.display = 'none';
    }
});

    </script>
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
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php $totalPrice = 0; $totalQuantity = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['pname']); ?></td>
                            <td>
                                <input 
                                    type="number" 
                                    value="<?php echo $item['quantity']; ?>" 
                                    min="0" 
                                    class="product-quantity" 
                                    data-barcode="<?php echo $item['barcode']; ?>" 
                                    onchange="updateQuantity(this)">
                            </td>
                            
                            <td class="product-price"><?php echo number_format($item['price'] * $item['quantity'], 2); ?>¥</td>

                        </tr>
                        <?php 
                            $totalPrice += $item['price'] * $item['quantity']; 
                            $totalQuantity += $item['quantity'];
                        ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: gray;">商品が追加されていません。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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