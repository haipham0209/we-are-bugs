<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Thông tin kết nối cơ sở dữ liệu
include('./Manager/php/db_connect.php');

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Kiểm tra tham số URL
if (!isset($_GET['sname'])) {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

$storeName = $_GET['sname'];

// Khởi tạo các biến
$tel = null;
$address = null;
$mail = null;
$sname = null;
$storeid = null;

// Truy vấn dữ liệu cửa hàng
$query = "SELECT store.storeid, store.logopath, store.sname, store.tel, store.address, user.mail 
          FROM store 
          JOIN user ON store.userid = user.userid 
          WHERE store.sname = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $storeName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $storeData = $result->fetch_assoc();
    $storeid = $storeData['storeid'];
    $sname = $storeData["sname"];
    $tel = $storeData["tel"];
    $address = $storeData["address"];
    $mail = $storeData["mail"];
    $logopath = str_replace('../Manager/', './Manager/', $storeData["logopath"]);
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

// Truy vấn sản phẩm bán chạy
$best_sellers_sql = "
    SELECT 
        p.productid, 
        p.pname, 
        p.price, 
        p.productImage, 
        SUM(od.quantity) AS total_quantity
    FROM product p
    JOIN order_details od ON p.productid = od.productid
    WHERE p.storeid = ?
    GROUP BY p.productid, p.pname, p.price, p.productImage
    ORDER BY total_quantity DESC
    LIMIT 3";
$stmt_best_sellers = $conn->prepare($best_sellers_sql);
$stmt_best_sellers->bind_param("i", $storeid);
$stmt_best_sellers->execute();
$best_sellers_result = $stmt_best_sellers->get_result();

// Đóng kết nối
$stmt->close();
$stmt_best_sellers->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - Home</title>
    <!-- Bootstrap CSS (cục bộ) -->
    
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/main2.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="nav-menu">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <h3><?php echo $sname; ?></h3>
                        <li class="nav-item">
                            <a class="nav-link" href="./main.php?sname=<?= urlencode($sname) ?>">ホームページ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./html/storeInfor.php?sname=<?= urlencode($sname) ?>">お店について</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./html/myPage.php?sname=<?= urlencode($sname) ?>">マイページ</a>
                        </li>
                        <li class="support-title">サポート</li>
                        <li class="nav-item">
                            <i class="fa fa-phone"></i><a class="support" href="tel:"><?php echo $tel; ?></a>
                        </li>
                        <li class="nav-item">
                            <i class="fa fa-envelope"></i><a class="support" href="mail:"><?php echo $mail; ?></a>
                        </li>
                        <li class="nav-item">
                        <i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a>
                        </li>
                    </ul>
                </div>
                <!-- <div class="overlay"></div> -->
                <a class="navbar-brand" href="#"><img id="logoContainer" src="<?= $logopath ?>" alt="logo"></a>
                <div id="searchContainer" class="d-none">
                    <input type="text" id="searchInput" class="form-control" placeholder="商品を検索">
                </div>
                <button id="searchBtn" class="btn btn-outline-primary me-2">
                    <i class="fa fa-search"></i>
                </button>
               
            </div>
           
        </nav>
    </header>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchBtn = document.getElementById("searchBtn");
            const searchContainer = document.getElementById("searchContainer");
            const logoContainer = document.getElementById("logoContainer");

            searchBtn.addEventListener("click", function () {
                if (searchContainer.classList.contains("d-none")) {
                    // Hiển thị thanh tìm kiếm và ẩn logo
                    searchContainer.classList.remove("d-none");
                    logoContainer.classList.add("hidden");
                    document.getElementById("searchInput").focus(); // Đặt con trỏ vào thanh input
                } else {
                    // Ẩn thanh tìm kiếm và hiển thị logo
                    searchContainer.classList.add("d-none");
                    logoContainer.classList.remove("hidden");
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
            const menuButton = document.querySelector(".navbar-toggler");
            const navMenu = document.querySelector(".nav-menu");
            const overlay = document.querySelector(".overlay");

            // Xử lý mở menu
            menuButton.addEventListener("click", function () {
                navMenu.classList.toggle("open");
                overlay.classList.toggle("show");
            });

            // Xử lý đóng menu khi nhấn overlay
            overlay.addEventListener("click", function () {
                navMenu.classList.remove("open");
                overlay.classList.remove("show");
            });
        });

    </script>


    <main class="container mt-4">
        <!-- Best Sellers Section -->
        <h2 class="mb-4">Best Sellers</h2>
        <div class="row">
            <?php
            if ($best_sellers_result->num_rows > 0) {
                while ($product = $best_sellers_result->fetch_assoc()) {
                    $productImagePath = $product['productImage'];
                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</h5>
                                <p class="card-text">¥' . number_format($product['price'], 0) . '</p>
                            </div>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>
    </main>

    <!-- Bootstrap JS (cục bộ) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
