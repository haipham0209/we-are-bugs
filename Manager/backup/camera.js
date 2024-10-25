let scannerRunning = false; // Trạng thái của camera

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
    Quagga.onDetected((data) => {
        const code = data.codeResult.code;
        document.getElementById('barcode').value = code; // Điền mã vào ô input
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
document.getElementById('start-scan').addEventListener('click', startScanner);
