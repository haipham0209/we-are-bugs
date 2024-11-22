<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');

// Kết nối cơ sở dữ liệu
include('./php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy storeid từ session hoặc từ một nguồn khác
$storeid = $_SESSION['storeid']; // Assuming storeid is stored in session

// Lấy danh sách các danh mục từ cơ sở dữ liệu của người dùng hiện tại
$category_sql = "SELECT category_id, cname FROM category WHERE storeid = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $storeid); // Ràng buộc biến storeid
$stmt->execute();
$category_result = $stmt->get_result();

// Lấy category_id từ GET parameter (nếu có)
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Truy vấn để lấy danh sách sản phẩm từ cơ sở dữ liệu
$product_sql = "
    SELECT p.productid, p.pname, p.price, p.costPrice, p.description, p.stock_quantity, p.productImage, 
    u.username, c.cname 
    FROM product p
    JOIN store s ON p.storeid = s.storeid
    JOIN user u ON s.userid = u.userid
    JOIN category c ON p.category_id = c.category_id AND p.storeid = c.storeid
    WHERE p.storeid = ?
";

if ($category_id > 0) {
    $product_sql .= " AND p.category_id = ?";
}

$product_stmt = $conn->prepare($product_sql);

if ($category_id > 0) {
    $product_stmt->bind_param("ii", $storeid, $category_id);
} else {
    $product_stmt->bind_param("i", $storeid);
}

$product_stmt->execute();
$product_result = $product_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/proMana.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="./scripts/cameraScan.js"></script>
    <title>商品管理</title>
</head>

<body>
    <header>
        <!-- Header navbar -->
        <div class="main-navbar">
            <div class="search-scan">
                <input type="text" class="search-bar" placeholder="Search...">
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
        <div class="container">
            <p class="title">商品管理</p>

            <!-- Category -->
            <div class="category">
                <button class="all-categories <?= $category_id === 0 ? 'active' : '' ?>" onclick="showAllCategories()">All</button>
                <div class="main-category">
                    <?php
                    if ($category_result->num_rows > 0) {
                        while ($row = $category_result->fetch_assoc()) {
                            $activeClass = ($row['category_id'] === $category_id) ? 'active' : '';
                            echo '<button class="' . $activeClass . '" onclick="filterCategory(' . $row['category_id'] . ')">'
                                . htmlspecialchars($row['cname'], ENT_QUOTES, 'UTF-8') . '</button>';
                        }
                    } else {
                        echo '<p>No categories found.</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Add Product Button -->
            <div class="add-product">
                <a href="productAdd.php">
                    <button>+</button>
                </a>
            </div>

            <!-- Product Cards -->
            <div class="all-product">
                <?php
                if ($product_result->num_rows > 0) {
                    while ($product = $product_result->fetch_assoc()) {
                        $productImagePath = '../' . $product['productImage'];

                        echo '
                            <div class="product-card">
                               <a href="productEdit.php?id=' . $product['productid'] . '" class="edit-icon">
                                    <img src="../images/edit.png" alt="Edit">
                                </a>
                                <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" alt="Product Image">
                                <div class="product-info">
                                    <p><strong>名前：</strong>' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>カテゴリー：</strong>' . htmlspecialchars($product['cname'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>原価：</strong>' . htmlspecialchars($product['costPrice'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>値段：</strong>' . htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') . '</p>
                                    <p><strong>説明：</strong>' . htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') . '</p>
                                </div>
                                <div class="stock">在庫: ' . htmlspecialchars($product['stock_quantity'], ENT_QUOTES, 'UTF-8') . '</div>
                            </div>';
                    }
                } else {
                    echo '<p>No products found.</p>';
                }
                ?>
            </div>
        </div>
        <script>
            function filterCategory(categoryId) {
                const url = new URL(window.location.href);
                url.searchParams.set('category_id', categoryId);
                window.location.href = url.toString();
            }

            function showAllCategories() {
                const url = new URL(window.location.href);
                url.searchParams.delete('category_id');
                window.location.href = url.toString();
            }
        </script>
    </main>
    <footer></footer>
</body>

</html>