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

//eror//
// Kiểm tra xem có lỗi trong URL hay không và lấy các giá trị từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
$productname = isset($_GET['productname']) ? urldecode($_GET['productname']) : '';
$price = isset($_GET['price']) ? urldecode($_GET['price']) : '';
$costPrice = isset($_GET['costprice']) ? urldecode($_GET['costprice']) : '';
$description = isset($_GET['description']) ? urldecode($_GET['description']) : '';
$stockQuantity = isset($_GET['stock']) ? urldecode($_GET['stock']) : '';
$barcode = isset($_GET['barcode']) ? urldecode($_GET['barcode']) : '';
//end

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductAdd</title>
    <!-- <link rel="stylesheet" href="../styles/All.css"> -->
    <link rel="stylesheet" href="./styles/addProduct.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <main>
        <h3>商品追加</h3>
        <div class="addContainer">
            <form class="proAddForm" action="./php/addProductP.php" method="POST" enctype="multipart/form-data">
                <!-- Trường chọn ảnh -->
                <label for="productImage">商品画像:</label>
                <?php if ($error === 'imgerrort'): ?>
                    <p style="color: red; margin:0;">画像をアップロードしてください。</p>
                <?php endif; ?>
                <input type="file" id="productImage" name="productImage" accept="image/*" onchange="previewImage(event)">
                <br>
                <img id="imagePreview" src="#" alt="プレビュー画像" style="display:none; max-width:200px; margin-top:10px;">

                <!-- Category -->
                <label for="category">カテゴリー:</label>
                <?php if ($error === 'notexistcate'): ?>
                    <p style="color: red; margin:0;">カテゴリーを選ぶか入力してください</p>
                <?php endif; ?>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="categoryText" name="categoryText" placeholder="選択してください" readonly />
                    <select id="category" name="category" required onchange="handleCategorySelection()">
                        <option value="">選択してください</option>
                        <option value="new">新しいカテゴリーを追加</option>
                        <?php
                        if ($category_result->num_rows > 0) {
                            while ($row = $category_result->fetch_assoc()) {
                                echo "<option value='" . $row['category_id'] . "'>" . htmlspecialchars($row['cname']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <br>

                <!-- JavaScript để cập nhật ô văn bản -->
                <script>
                    function previewImage(event) {
                        const imagePreview = document.getElementById('imagePreview');
                        imagePreview.src = URL.createObjectURL(event.target.files[0]);
                        imagePreview.style.display = 'block';
                    }

                    function handleCategorySelection() {
                        const categoryDropdown = document.getElementById('category');
                        const categoryTextInput = document.getElementById('categoryText');

                        if (categoryDropdown.value === "new") {
                            categoryTextInput.placeholder = "新しいカテゴリー名を入力してください";
                            categoryTextInput.value = ""; // Clear the input field
                            categoryTextInput.removeAttribute('readonly');
                        } else {
                            categoryTextInput.value = categoryDropdown.options[categoryDropdown.selectedIndex].text;
                            categoryTextInput.setAttribute('readonly', true);
                        }
                    }
                </script>

                <!-- Các trường khác -->
                <?php if ($error === 'exist'): ?>
                    <p style="color: red; margin:0;">商品名またはバーコードが既に存在します</p>
                <?php endif; ?>
                <label for="pname">商品名:</label>
                <input type="text" id="pname" name="pname" required value="<?php echo htmlspecialchars($productname); ?>">
                <br>

                <label for="price">価格:</label>
                <input type="number" id="price" name="price" required min="0" step="0.1" value="<?php echo htmlspecialchars($price); ?>">
                <br>

                <label for="costPrice">仕入れ価格:</label>
                <input type="number" id="costPrice" name="costPrice" required min="0" step="0.1" value="<?php echo htmlspecialchars($costPrice); ?>">
                <br>

                <label for="description">商品説明:</label>
                <textarea id="description" name="description" rows="4" cols="50" required><?php echo htmlspecialchars($description); ?></textarea>
                <br>

                <label for="stockQuantity">在庫数量:</label>
                <input type="number" id="stockQuantity" name="stockQuantity" required min="1" value="<?php echo htmlspecialchars($stockQuantity); ?>">
                <br>

                <?php if ($error === 'exist'): ?>
                    <p style="color: red; margin:0;">商品名またはバーコードが既に存在します</p>
                <?php endif; ?>
                <label for="barcode">バーコード:</label>
                <input type="text" id="barcode" name="barcode" required value="<?php echo htmlspecialchars($barcode); ?>">
                <button type="button" id="start-scan">カメラでスキャン</button>
                <!-- Div để hiển thị camera -->
                <div id="camera" style="display: none;"></div>

                <button type="submit">商品を追加する</button>
            </form>
            <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center;">
                <div id="lottie"></div>
            </div>
            <script>
                // Lottie 起動
                document.addEventListener('DOMContentLoaded', function() {
                    // Lottie
                    const animation = lottie.loadAnimation({
                        container: document.getElementById('lottie'),
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        path: './images/loading.json'
                    });

                    // Hiện loading animation khi form được gửi
                    document.querySelector('.proAddForm').addEventListener('submit', function(event) {
                        document.getElementById('loading').style.display = 'flex';
                        // Không cần dùng setTimeout, form sẽ tự động gửi
                    });
                });
            </script>
        </div>
    </main>
    <script src="./scripts/camera.js"></script>
</body>

</html>

<?php
// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>