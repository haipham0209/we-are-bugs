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
require "resources.php";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - Home</title>
    <!-- Bootstrap CSS (cục bộ) -->
    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

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
                <a class="navbar-brand" href="#"><img id="logoContainer" src="<?= $logopath ?>" alt="logo"></a>
                <div class="nav-menu">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="./main2.php?sname=<?= urlencode($sname) ?>">ホームページ</a>
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
        <!-- <h2 class="mb-4">Best Sellers</h2>
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
        </div> -->
        <!-- <p>------------------------------------------------------------------------</p> -->
        <section id="product-section" class="category">
            <?php foreach ($categories as $category): ?>
                <div class="group" id="<?= htmlspecialchars(strtolower($category['cname'])) ?>">
                    <h3 class="title"><?= htmlspecialchars($category['cname']) ?></h3>
                    <div class="product-showcase">
                        <!-- Hiển thị tối đa 2 sản phẩm -->
                        <?php 
                        $productCount = 0;
                        foreach ($category['products'] as $product): 
                            if ($productCount >= 2) break; // Dừng khi đã hiển thị đủ 4 sản phẩm
                            $productCount++;
                        ?>
                            <div class="product-content" data-aos="fade-up" data-aos-duration="1000">
                                <img src="<?= htmlspecialchars($product['productImage']) ?>" alt="<?= htmlspecialchars($product['pname']) ?>" class="product-image"/>
                                <p class="rotated-text"><?= htmlspecialchars($product['pname']) ?><br><?= number_format($product['price']) ?> ¥</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Nếu số lượng sản phẩm > 2, hiển thị nút Show More -->
                    <?php if (count($category['products']) > 2): ?>
                        <button class="show-more-btn" data-group="<?= htmlspecialchars(strtolower($category['cname'])) ?>" onclick="showMore(<?= htmlspecialchars(json_encode($category['products'])) ?>)">Show More</button>
                        <!-- <button class="show-more-btn" data-group="<?= htmlspecialchars(strtolower($category['cname'])) ?>"
                            onclick="toggleShowMore(this, <?= htmlspecialchars(json_encode($category['products'])) ?>)">
                            Show More
                        </button> -->

                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>


    </main>

    <!-- Bootstrap JS (cục bộ) -->
     <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            AOS.init({
                duration: 1000, // Thời gian hiệu ứng (ms)
                easing: 'ease-in-out', // Loại easing cho hiệu ứng
                once: true // Hiệu ứng chỉ diễn ra một lần khi cuộn
            });
        });
        function showMore(products) {
    const productShowcase = event.target.previousElementSibling;
    const button = event.target; // Nút Show More/Show Less hiện tại
    const displayedProducts = productShowcase.querySelectorAll('.product-content');

    // Kiểm tra trạng thái của nút
    if (button.textContent === "Show More") {
        // Chỉ lấy các sản phẩm chưa hiển thị
        const remainingProducts = products.slice(displayedProducts.length);

        remainingProducts.forEach(product => {
            const productContent = document.createElement('div');
            productContent.classList.add('product-content');
            productContent.setAttribute('data-aos', 'fade-up');
            productContent.setAttribute('data-aos-duration', '1000');
            productContent.innerHTML = `
                <img src="${product.productImage}" alt="${product.pname}" class="product-image"/>
                <p class="rotated-text">${product.pname}<br>${product.price} ¥</p>
            `;
            productShowcase.appendChild(productContent);
        });

        // Đổi nút thành Show Less
        button.textContent = "Show Less";
    } else {
        // Quay về trạng thái chỉ hiển thị 2 sản phẩm đầu tiên
        productShowcase.innerHTML = ''; // Xóa toàn bộ sản phẩm hiện tại

        // Chỉ hiển thị 2 sản phẩm đầu
        products.slice(0, 2).forEach(product => {
            const productContent = document.createElement('div');
            productContent.classList.add('product-content');
            // productContent.setAttribute('data-aos', 'fade-up');
            // productContent.setAttribute('data-aos-duration', '1000');
            productContent.innerHTML = `
                <img src="${product.productImage}" alt="${product.pname}" class="product-image"/>
                <p class="rotated-text">${product.pname}<br>${product.price} ¥</p>
            `;
            productShowcase.appendChild(productContent);
        });
        productShowcase.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Đổi nút thành Show More
        button.textContent = "Show More";
    }
}

    </script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
<body onload="window.scrollTo(0, 0);">

</html>
