<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');

// Connect to the database
include('./php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the product ID from the URL
$productid = $_GET['id'] ?? null;

// If product ID is not provided, redirect or show an error
if (!$productid) {
    die("Product ID not provided.");
}

// Query to get the product details
$product_sql = "
    SELECT p.productid, p.pname, p.price, p.costPrice, p.description, p.stock_quantity, p.productImage, p.category_id, 
           p.barcode, c.cname AS category_name
    FROM product p
    JOIN category c ON p.category_id = c.category_id
    WHERE p.productid = ?";

$stmt = $conn->prepare($product_sql);
$stmt->bind_param("i", $productid);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    die("Product not found.");
}

// Retrieve category options
$category_sql = "SELECT category_id, cname FROM category WHERE storeid = ?";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->bind_param("i", $_SESSION['storeid']); // Assuming storeid is stored in session
$category_stmt->execute();
$category_result = $category_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
    <!-- <link rel="stylesheet" href="../styles/All.css"> -->
    <link rel="stylesheet" href="./styles/productEdit.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <main>
        <div class="head">
        <h3>商品編集</h3>
        <h3><a href="main.php"><img class="home" src="./images/home.png" alt="Home Mana"></a></h3>
        </div>
        <div class="addContainer">
            <form class="proAddForm" action="./php/updateProduct.php" method="POST" enctype="multipart/form-data">
                <!-- Thêm icon thùng rác -->
            <img src="../images/delete-icon.png" alt="削除" id="deleteIcon" onclick="deleteProduct()">
                <!-- Trường chọn ảnh -->
                <div class="imgDiv">
                    <label for="productImage">商品画像:</label>
                    <div class="imageContainer">
                        <img id="imagePreview" src="../<?= htmlspecialchars($product['productImage']); ?>" alt="プレビュー画像">
                    </div>
                    <!-- <input type="file" id="productImage" name="productImage" accept="image/*" onchange="previewImage(event)"> -->
                </div>

                 <!-- Thêm trường ẩn cho ảnh cũ -->
                 <input type="hidden" name="currentProductImage" value="../<?= htmlspecialchars($product['productImage']); ?>">


                <!-- Category -->
                <label for="category">カテゴリー:</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="categoryText" name="categoryText" placeholder="選択したカテゴリー" value="<?= htmlspecialchars($product['category_name']); ?>" />
                    <select id="category" name="category" required onchange="updateCategoryText()">
                        <option value="">選択してください</option>
                        <?php
                        if ($category_result->num_rows > 0) {
                            while ($row = $category_result->fetch_assoc()) {
                                $selected = ($row['category_id'] == $product['category_id']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($row['category_id']) . "' $selected>" . htmlspecialchars($row['cname']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>カテゴリーがありません</option>";
                        }
                        ?>
                    </select>
                </div>
                <br>

                <!-- JavaScript để cập nhật ô văn bản -->
                <script>
                    function updateCategoryText() {
                        const categoryDropdown = document.getElementById('category');
                        const categoryTextInput = document.getElementById('categoryText');
                        categoryTextInput.value = categoryDropdown.options[categoryDropdown.selectedIndex].text;
                    }
                </script>

                <!-- Các trường khác -->
                <label for="pname">商品名:</label>
                <input type="text" id="pname" name="pname" value="<?= htmlspecialchars($product['pname']); ?>" readonly>
                <br>

                <label for="price">価格:</label>
                <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']); ?>" required min="0" step="0.1">
                <br>

                <label for="costPrice">仕入れ価格:</label>
                <input type="number" id="costPrice" name="costPrice" value="<?= htmlspecialchars($product['costPrice']); ?>" required min="0" step="0.1">
                <br>

                <label for="description">商品説明:</label>
                <textarea id="description" name="description" rows="4" cols="50" required><?= htmlspecialchars($product['description']); ?></textarea>
                <br>

                <!-- Số lượng tồn kho -->
                <label for="stockQuantity">在庫数量:</label>
                <input type="number" id="stockQuantity" name="stockQuantity" value="<?= htmlspecialchars($product['stock_quantity']); ?>" required min="1">
                <br>

                <label for="barcode">バーコード:</label>
                <input type="text" id="barcode" name="barcode" value="<?= htmlspecialchars($product['barcode']); ?>" readonly>
                <!-- <button type="button" id="start-scan">カメラでスキャン</button> -->
                <!-- Div để hiển thị camera -->
                <!-- <div id="camera" style="display: none;"></div> -->

                <button type="submit">商品情報を更新する</button>
                 <!-- Thêm trường hidden để gửi productid -->
                 <input type="hidden" name="productid" value="<?= htmlspecialchars($product['productid']); ?>">
            </form>
        </div>
    </main>
    <!-- <script src="./scripts/camera.js"></script> -->
    <script>
        // Xóa sản phẩm
        function deleteProduct() {
            if (confirm('商品を完全に削除します。よろしいですか')) {
                window.location.href = './php/deleteProduct.php?id=<?= $productid ?>';
            }
        }
    </script>
</body>

</html>

<?php
// Đóng kết nối cơ sở dữ liệu
// $stmt->close();
// $conn->close();
?>