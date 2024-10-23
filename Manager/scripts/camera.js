
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
