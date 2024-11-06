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

// Truy vấn để lấy danh sách sản phẩm từ cơ sở dữ liệu
// $product_sql = "SELECT pname, price, costPrice, description, stock_quantity, productImage FROM product WHERE storeid = ?";
$product_sql = "
    SELECT p.pname, p.price, p.costPrice, p.description, p.stock_quantity, p.productImage, 
           u.username, c.cname 
    FROM product p
    JOIN store s ON p.storeid = s.storeid
    JOIN user u ON s.userid = u.userid
    JOIN category c ON p.category_id = c.category_id
    WHERE p.storeid = ?";

$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $storeid);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/proMana.css">
    <title>商品管理</title>
</head>

<body>
    <header>
         <!-- Header navbar -->
         <div class="main-navbar">
            <div class="search-scan"> 
                <input type="text" class="search-bar" placeholder="Search...">
                <img src="./images/camera-icon.png" class="camera-icon" onclick="openCamera()">
                <div id="camera"></div>
            </div>
            <button class="main-home">
                <h1 class="logo">WRB</h1>
            </button>
        </div>
        <script>
            //gọi hàm kích hoạt camera
        </script>
    </header>
    <main>
        <div class="container">
            <p class="title">商品管理</p>
            <!-- Category -->
            <div class="main-category">
                <?php
                if ($category_result->num_rows > 0) {
                    // Duyệt qua các hàng kết quả từ truy vấn
                    while ($row = $category_result->fetch_assoc()) {
                        // Hiển thị từng danh mục dưới dạng button
                        echo '<button>' . htmlspecialchars($row['cname'], ENT_QUOTES, 'UTF-8') . '</button>';
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
                        // Duyệt qua danh sách sản phẩm và hiển thị mỗi sản phẩm dưới dạng card
                        while ($product = $product_result->fetch_assoc()) {
                            // Xây dựng đường dẫn ảnh từ username, cname và productImage
                            $productImagePath = '../' .$product['productImage'];
                            
                            echo '
                            <div class="product-card">
                                <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" alt="Product Image">
                                <div class="product-info">
                                    <p><strong>名前：</strong>' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</p>
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
        <script src="../scripts/camera.js"></script>
    </main>
    <footer>    
    </footer>
</body>

</html>