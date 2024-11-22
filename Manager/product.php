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

// Lấy category_ids từ GET parameter (nếu có)
$category_ids = isset($_GET['category_ids']) ? explode(',', $_GET['category_ids']) : [];

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

if (!empty($category_ids)) {
    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $product_sql .= " AND p.category_id IN ($placeholders)";
}

$product_stmt = $conn->prepare($product_sql);

// バインド変数を動的に設定
$params = array_merge([$storeid], $category_ids);
$types = str_repeat('i', count($params)); // 全て整数型
$product_stmt->bind_param($types, ...$params);

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
        <p class="title">商品管理</p>

        <!-- Category -->
        <div class="category">
            <button class="all-categories <?= empty($category_ids) ? 'active' : '' ?>" onclick="showAllCategories()">All</button>
            <?php
            if ($category_result->num_rows > 0) {
                while ($row = $category_result->fetch_assoc()) {
                    $isSelected = in_array($row['category_id'], $category_ids) ? 'active' : '';
                    echo '<button class="' . $isSelected . '" data-category-id="' . $row['category_id'] . '">'
                        . htmlspecialchars($row['cname'], ENT_QUOTES, 'UTF-8') . '</button>';
                }
            } else {
                echo '<p>No categories found.</p>';
            }
            ?>
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
        <script>
            const selectedCategories = new Set(<?= json_encode($category_ids) ?>);

            // ボタンのクリックイベントを設定
            document.querySelectorAll('.category button').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.classList.contains('all-categories')) {
                        // "All" がクリックされた場合、選択をクリア
                        selectedCategories.clear();
                        document.querySelectorAll('.category button').forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                    } else {
                        // その他のカテゴリボタンがクリックされた場合
                        const categoryId = this.getAttribute('data-category-id');

                        if (selectedCategories.has(categoryId)) {
                            selectedCategories.delete(categoryId);
                            this.classList.remove('active');
                        } else {
                            selectedCategories.add(categoryId);
                            this.classList.add('active');
                        }

                        // "All" ボタンの選択解除
                        document.querySelector('.all-categories').classList.remove('active');
                    }

                    // URLを更新
                    updateUrl();
                });
            });

            function showAllCategories() {
                selectedCategories.clear();
                updateUrl();
            }

            function updateUrl() {
                const url = new URL(window.location.href);
                if (selectedCategories.size === 0) {
                    url.searchParams.delete('category_ids');
                } else {
                    url.searchParams.set('category_ids', Array.from(selectedCategories).join(','));
                }
                window.location.href = url.toString();
            }
        </script>
    </main>
    <footer></footer>
</body>

</html>