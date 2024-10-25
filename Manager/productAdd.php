<script>
    console.log(document.cookie);
</script>
<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
// include('./php/auth_check.php');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
    <title>Document</title>
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="./styles/addProduct.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
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
                        <option value="1">WOMEN</option>
                        <option value="2">WOMEN</option>
                        <option value="3">CHILD</option>
                        <option value="4">OTHE</option>
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

                <!-- Tên sản phẩm -->
                <label for="pname">商品名:</label>
                <input type="text" id="pname" name="pname" required>
                <br>

                <!-- Giá bán -->
                <label for="price">価格:</label>
                <input type="number" id="price" name="price" required min="0" step="0.01">
                <br>

                <!-- Giá nhập hàng -->
                <label for="costPrice">仕入れ価格:</label>
                <input type="number" id="costPrice" name="costPrice" required min="0" step="0.01">
                <br>

                <!-- Mô tả sản phẩm -->
                <label for="description">商品説明:</label>
                <textarea id="description" name="description" rows="4" cols="50" required></textarea>
                <br>

                <!-- Số lượng trong kho -->
                <label for="stockQuantity">在庫数量:</label>
                <input type="number" id="stockQuantity" name="stockQuantity" required min="0">
                <br>

                <!-- Barcode -->
                <label for="barcode">バーコード:</label>
                <input type="text" id="barcode" name="barcode" required>
                <button type="button" id="start-scan">カメラでスキャン</button>

                <!-- Div để hiển thị camera -->
                <div id="camera" style="display: none;"></div>

                <button type="submit">商品を追加する</button>
            </form>
        </div>
    </main>
    <footer></footer>

    <script src="./scripts/camera.js"></script>
</body>

</html>