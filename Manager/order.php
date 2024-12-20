<?php
include('./php/auth_check.php'); 
include('./php/db_connect.php');
include('./php/order_process.php');

// Khởi tạo kết nối cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/order.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <!-- <script src="./scripts/cameraPos.js"></script> -->
    <!-- <script src="./scripts/search.js"></script> -->
    

    <title>Order History</title>
</head>

<body>
    <header>
        <div class="main-navbar">
            <div class="search-scan">
                <input type="text" name="barcode" id="barcode-input" class="search-bar" placeholder="商品名又はコード入力">            
                <div id="barcode-suggestions" class="suggestions-list" style="display:none;"></div>
                <img src="./images/camera-icon.png" class="camera-icon" onclick="toggleCamera()">
            </div>
            <div id="suggestionList"></div>
            <script>
                let isCameraRunning = false; 

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
            <a href="main.php">
                <img class="home" src="./images/home.png" alt="Home Mana">
            </a>
        </div>
    </header>
    <main>   
        <div id="camera" style="display: none;">
            <button id="stopBtn" onclick="toggleCamera()">カメラ停止</button>
        </div>
        <p class="title">注文履歴</p>
        <!-- 日期顯示 -->
        <div class="date-control">
            <button id="prev-date" class="date-button">◀</button>
            <input type="date" id="date-picker" class="date-picker">
            <button id="next-date" class="date-button">▶</button>
        </div>
        <script src="./scripts/dateorder.js"></script>
        <!-- <script src="./scripts/order.js"></script> -->
        <!-- Bảng danh sách đơn hàng -->
        <table class="order-list">
            <thead>
                <tr>
                    <th>番号</th>
                    <th>注文番号</th>
                    <th>数量</th>
                    <th>金額</th>
                </tr>
            </thead>
            <tbody id="order-list-body">
                <!-- Dữ liệu sẽ được chèn bằng JavaScript -->
            </tbody>
        </table>

        <!-- Chi tiết đơn hàng -->
        <div id="order-details" style="display: none;">
            <h3>注文の詳細</h3>
            <table>
                <thead>
                    <tr>
                        <th>商品名</th>
                        <th>数量</th>
                        <th>単価</th>
                        <th>小計</th>
                    </tr>
                </thead>
                <tbody id="order-details-body">
                    <!-- Dữ liệu chi tiết sẽ được chèn bằng JavaScript -->
                </tbody>
            </table>
            <p id="order-summary"></p>
        </div>
    </main>
    <footer></footer>
</body>

</html>
