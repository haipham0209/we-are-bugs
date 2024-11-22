let scannerRunning = false; // Trạng thái của camera

// カメラのスキャン機能の開始・停止を切り替える
function toggleScanner() {
    if (scannerRunning) {
        stopScanner(); // カメラを停止
    } else {
        startScanner(); // カメラを開始
    }
}

// Khởi động quét mã
function startScanner() {
    if (scannerRunning) return; // Nếu đã chạy thì không khởi động lại

    scannerRunning = true;
    const cameraDiv = document.getElementById('camera');
    cameraDiv.style.display = 'block'; // Hiện camera

    Quagga.init(
        {
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: cameraDiv,
            },
            decoder: {
                readers: ["ean_reader", "code_128_reader", "upc_reader"], // Các loại barcode
            },
        },
        (err) => {
            if (err) {
                console.error(err);
                alert("カメラを起動できませんでした。");
                stopScanner();
                return;
            }
            Quagga.start();
        }
    );

    // Khi phát hiện mã, tắt camera và điền vào ô barcode
// Khi phát hiện mã, phát sự kiện 'barcodeDetected' với mã vạch
Quagga.onDetected((data) => {
    const code = data.codeResult.code;

    // Gửi mã vạch đến server
    fetch('./php/getProductByBarcode.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ barcode: code }),
    })
    .then((response) => response.json())
    .then((product) => {
        if (product && product.productid) {
            // Chuyển hướng đến trang chỉnh sửa sản phẩm
            window.location.href = `./productEdit.php?id=${product.productid}`;
        } else {
            alert('Product not found!');
            // console.log(product);
            // console.log(product.productid);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });

    stopScanner();
});


}

// Dừng quét mã và tắt camera
function stopScanner() {
    if (!scannerRunning) return; // Nếu camera chưa bật thì bỏ qua

    Quagga.stop();
    document.getElementById('camera').style.display = 'none'; // Ẩn camera
    scannerRunning = false;
}

// Gắn sự kiện cho nút bắt đầu quét
document.getElementById('start-scan').addEventListener('click', toggleScanner);
