<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "dbuser";
$password = "ecc";
$dbname = "wearebugs";
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy userid từ session
$userid = $_SESSION['userid'];

// Lấy danh sách các danh mục từ cơ sở dữ liệu của người dùng hiện tại
$category_sql = "SELECT category_id, cname FROM category WHERE userid = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $userid); // Ràng buộc biến userid
$stmt->execute();
$category_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="./styles/addProduct.css">
</head>
<body>
    <header></header>
    <main>
        <h3>商品追加</h3>
        <div class="addContainer">
            <form class="proAddForm" action="./php/addProductP.php" method="POST" enctype="multipart/form-data">
                <!-- Trường chọn ảnh -->
                <label for="productImage">商品画像:</label>
                <input type="file" id="productImage" name="productImage" accept="image/*" onchange="previewImage(event)">
                <br>
                <img id="imagePreview" src="#" alt="プレビュー画像" style="display:none; max-width:200px; margin-top:10px;">

                <!-- Category -->
                <label for="category">カテゴリー:</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="categoryText" name="categoryText" placeholder="選択したカテゴリー" />
                    <select id="category" name="category" required onchange="updateCategoryText()">
                        <option value="">選択してください</option>
                        <?php
                        if ($category_result->num_rows > 0) {
                            while ($row = $category_result->fetch_assoc()) {
                                echo "<option value='" . $row['category_id'] . "'>" . htmlspecialchars($row['cname']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>Không có danh mục nào</option>";
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
                <input type="text" id="pname" name="pname" required>
                <br>

                <label for="price">価格:</label>
                <input type="number" id="price" name="price" required min="0" step="0.01">
                <br>

                <label for="costPrice">仕入れ価格:</label>
                <input type="number" id="costPrice" name="costPrice" required min="0" step="0.01">
                <br>

                <label for="description">商品説明:</label>
                <textarea id="description" name="description" rows="4" cols="50" required></textarea>
                <br>

                <label for="stockQuantity">在庫数量:</label>
                <input type="number" id="stockQuantity" name="stockQuantity" required min="0">
                <br>

                <label for="barcode">バーコード:</label>
                <input type="text" id="barcode" name="barcode" required>
                <button type="button" id="start-scan">カメラでスキャン</button>

                <button type="submit">商品を追加する</button>
            </form>
        </div>
    </main>
    <footer></footer>
</body>
</html>

<?php
// Đóng kết nối cơ sở dữ liệu
$stmt->close();
$conn->close();
?>
