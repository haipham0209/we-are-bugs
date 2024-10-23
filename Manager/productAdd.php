<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./styles/addProduct.css">
    <link rel="stylesheet" href="../styles/All.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <!-- <script src="html5-qrcode.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script> -->
</head>
<body>
    <header>

    </header>
    <main>
    <h3>商品追加</h3>
    <div class="addContainer">
        <form class="proAddForm" action="" method="POST" enctype="multipart/form-data">
            <!-- Trường chọn ảnh -->
            <label for="productImage">商品画像:</label>
            <input type="file" id="productImage" name="productImage" accept="image/*" onchange="previewImage(event)">
            <br>
            <img id="imagePreview" src="#" alt="プレビュー画像" style="display:none; max-width:200px; margin-top:10px;">
            
            <!-- Category -->
            <label for="category">カテゴリー:</label>
            <select id="category" name="category" required>
                <option value="men">Men</option>
                <option value="women">Women</option>
                <option value="child">Child</option>
            </select>
            <br>

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

            <!-- Tên sản phẩm -->
            <label for="barcode">バーコード:</label>
            <input type="text" id="barcode" name="barcode" required>
            <button type="button" id="start-scan">カメラでスキャン</button>
            <!-- Div để hiển thị camera -->
    <div id="barcode-scanner" style="display : none;"></div>

<script>
    const startScanButton = document.getElementById('start-scan');
    const barcodeInput = document.getElementById('barcode');
    const scannerDiv = document.getElementById('barcode-scanner');
    let html5QrcodeScanner;

    // Bắt đầu hoặc dừng quét camera
    startScanButton.addEventListener('click', () => {
        if (scannerDiv.style.display === 'none') {
            scannerDiv.style.display = 'block';
            startCamera();
            startScanButton.textContent = '停止';  // Nút đổi thành "Dừng"
        } else {
            stopCamera();
            startScanButton.textContent = 'カメラでスキャン';  // Nút đổi thành "Bật Camera"
        }
    });

    // Khởi động camera và quét mã barcode
    function startCamera() {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "barcode-scanner", { fps: 5, qrbox: 250 });

        html5QrcodeScanner.render(onScanSuccess, onScanError);
    }

    // Xử lý khi quét thành công
    function onScanSuccess(decodedText) {
        barcodeInput.value = decodedText;  // Gán mã barcode vào input
        stopCamera();  // Tự động dừng sau khi quét thành công
        startScanButton.textContent = 'カメラでスキャン';
    }

    // Xử lý lỗi trong quá trình quét (tùy chọn)
    let errorCount = 0;  // Đếm số lần lỗi

function onScanError(error) {
    errorCount++;
    if (errorCount % 5 === 0) {  // Chỉ log lỗi sau mỗi 5 lần thất bại
        console.warn(`Scan error: ${error}`);
    }
}


    // Dừng camera
    function stopCamera() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(error => console.error('Stop failed.', error));
        }
        scannerDiv.style.display = 'none';
    }
</script>

            <button type="submit">商品を追加する</button>
        </form>
    </div>
    <!-- <div id="barcode-scanner__dashboard_section" style="width: 100%; padding: 10px 0px; text-align: left;"><div><div id="barcode-scanner__dashboard_section_csr" style="display: block; text-align: center;"><div style="display: none; padding: 5px 10px; text-align: center;"><input id="html5-qrcode-input-range-zoom" class="html5-qrcode-element" type="range" min="1" max="5" step="0.1" style="display: inline-block; width: 50%; height: 5px; background: rgb(211, 211, 211); outline: none; opacity: 0.7;"><span style="margin-right: 10px;">1x zoom</span></div><span style="margin-right: 10px;">Select Camera (2)  <select id="html5-qrcode-select-camera" class="html5-qrcode-element" disabled=""><option value="3774e0c68f96806f939485e466e60d70fce564495b87a7d7c5c31da67117ee81">HD Webcam (5986:211b)</option><option value="28995ee4fdbaa1af0587a25003cb6f5de3f93303e36b8fecbdd9c89e5558f059">Intel Virtual Camera</option></select></span><span><button id="html5-qrcode-button-camera-start" class="html5-qrcode-element" type="button" style="opacity: 1; display: none;">Start Scanning</button><button id="html5-qrcode-button-camera-stop" class="html5-qrcode-element" type="button" style="display: inline-block;">Stop Scanning</button></span></div><div style="text-align: center; margin: auto auto 10px; width: 80%; max-width: 600px; border: 6px dashed rgb(235, 235, 235); padding: 10px; display: none;"><label for="html5-qrcode-private-filescan-input" style="display: inline-block;"><button id="html5-qrcode-button-file-selection" class="html5-qrcode-element" type="button">Choose Image - No image choosen</button><input id="html5-qrcode-private-filescan-input" class="html5-qrcode-element" type="file" accept="image/*" style="display: none;"></label><div style="font-weight: 400;">Or drop an image to scan</div></div></div><div style="text-align: center;"><span id="html5-qrcode-anchor-scan-type-change" class="html5-qrcode-element" style="text-decoration: underline; cursor: pointer; display: none;">Scan an Image File</span></div></div>
    <div id="barcode-scanner__dashboard_section" style="width: 100%; padding: 10px 0px; text-align: left;"><div><div id="barcode-scanner__dashboard_section_csr" style="display: block; text-align: center;"><div style="display: none; padding: 5px 10px; text-align: center;"><input id="html5-qrcode-input-range-zoom" class="html5-qrcode-element" type="range" min="1" max="5" step="0.1" style="display: inline-block; width: 50%; height: 5px; background: rgb(211, 211, 211); outline: none; opacity: 0.7;"><span style="margin-right: 10px;">1x zoom</span></div><span style="margin-right: 10px;">Select Camera (2)  <select id="html5-qrcode-select-camera" class="html5-qrcode-element" disabled=""><option value="3774e0c68f96806f939485e466e60d70fce564495b87a7d7c5c31da67117ee81">HD Webcam (5986:211b)</option><option value="28995ee4fdbaa1af0587a25003cb6f5de3f93303e36b8fecbdd9c89e5558f059">Intel Virtual Camera</option></select></span><span><button id="html5-qrcode-button-camera-start" class="html5-qrcode-element" type="button" style="opacity: 1; display: none;">Start Scanning</button><button id="html5-qrcode-button-camera-stop" class="html5-qrcode-element" type="button" style="display: inline-block;">Stop Scanning</button></span></div><div style="text-align: center; margin: auto auto 10px; width: 80%; max-width: 600px; border: 6px dashed rgb(235, 235, 235); padding: 10px; display: none;"><label for="html5-qrcode-private-filescan-input" style="display: inline-block;"><button id="html5-qrcode-button-file-selection" class="html5-qrcode-element" type="button">Choose Image - No image choosen</button><input id="html5-qrcode-private-filescan-input" class="html5-qrcode-element" type="file" accept="image/*" style="display: none;"></label><div style="font-weight: 400;">Or drop an image to scan</div></div></div><div style="text-align: center;"><span id="html5-qrcode-anchor-scan-type-change" class="html5-qrcode-element" style="text-decoration: underline; cursor: pointer; display: none;">Scan an Image File</span></div></div> -->

</main>
    <footer>

    </footer>
</body>
</html>