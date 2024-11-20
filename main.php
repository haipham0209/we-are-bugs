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

// Kiểm tra xem có tham số sname trong URL không
if (!isset($_GET['sname'])) {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

$storeName = $_GET['sname'];

// Khởi tạo các biến để tránh lỗi chưa khai báo
$tel = null;
$address = null;
$mail = null;
$sname = null;
$storeid = null;

// Thực hiện truy vấn để lấy dữ liệu cửa hàng và thông tin người dùng
$query = "SELECT store.storeid, store.sname, store.tel, store.address, user.mail 
          FROM store 
          JOIN user ON store.userid = user.userid 
          WHERE store.sname = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $storeName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $storeData = $result->fetch_assoc();
    $storeid = $storeData['storeid']; // Lấy storeid để sử dụng trong resources.php
    $sname = $storeData["sname"];
    $tel = $storeData["tel"];
    $address = $storeData["address"];
    $mail = $storeData["mail"];
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

// Đóng kết nối
$stmt->close();
$conn->close();

// Gọi file resources.php để hiển thị thông tin sản phẩm của cửa hàng
require "resources.php";
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - Home</title>
    <link rel="stylesheet" href="./styles/index.css">
    <link rel="stylesheet" href="./styles/All.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Lavishly+Yours&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Itim&family=Lavishly+Yours&display=swap" rel="stylesheet">


    
</head>
<body>
    <header>
        <!-- Navbar -->
        <div class="navbar">
            <button class="menu-toggle" aria-label="Toggle navigation">
                <span class="menu-icon"></span>
            </button>
            <div class="logo-container">
                <h1 class="logo">WRB</h1>
                <input type="text" class="search-bar" placeholder="Search...">
            </div>
            <button class="account-toggle">
                <img class="avatar" src="./images/avataricon.jpg" alt="Account icon" class="account-icon">
            </button>
        </div>

        <nav class="nav-menu">
            <ul>
            <li><h3><?php echo $sname; ?></h3></li>
            <li><a href="./main.php?sname=<?php echo $_GET['sname']  ?>">ホームページ</a></li>
            <li><a href="./html/product.php">商品</a></li>
            <!-- <li><a href="./html/storeInfor.php">お店について</a></li> -->
            <li><a href="./html/storeInfor.php?sname=<?php echo urlencode($sname); ?>">お店について</a></li>

            <!-- <li><a href="#">会員登録</a></li>
            <li><a href="#">ログイン</a></li> -->
            <li><a href="./html/myPage.php">マイページ</a></li>
            <li class="support-title">サポート</li>
            <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo $tel; ?>"><?php echo $tel; ?></a></li>
            <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mail:"><?php echo $mail; ?></a></li>
            <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a></li>
            </ul>
        </nav>
        <div class="overlay"></div>
        <nav class="nav-myPage">
            <ul>
                <li><a href="#">登録</a></li>
                <li><a href="#">ログイン</a></li>
            </ul>
        </nav>
        <div class="overlay-avatar"></div>
    </header>

    <main>
        <div class="filter-buttons">
            <button class="filter-button active" data-target="all">All</button>
            <button class="filter-button" data-target="men">Men</button>
            <button class="filter-button" data-target="women">Women</button>
            <button class="filter-button" data-target="child">Child</button>
        </div>
        <section class="best-sellers">
    <h2>Best Sellers</h2>
    <div class="slider">
        <button class="arrow left">&#10094;</button>
        <div class="product-grid">
            <div class="product">
                <img src="./images/facebook.png" alt="Facebook" />
                <p class="product-name">Facebook</p>
                <p class="product-price">$10.00</p>
            </div>
            <div class="product">
                <img src="./images/top_button.png" alt="Top Button" />
                <p class="product-name">Top Button</p>
                <p class="product-price">$15.00</p>
            </div>
            <div class="product">
                <img src="./images/twitter.png" alt="Twitter" />
                <p class="product-name">Twitter</p>
                <p class="product-price">$12.00</p>
            </div>
            <div class="product">
                <img src="./images/twitter.png" alt="Twitter" />
                <p class="product-name">Twitter</p>
                <p class="product-price">$12.00</p>
            </div>
            <div class="product">
                <img src="./images/twitter.png" alt="Twitter" />
                <p class="rotated-text">Twitter</p>
                <p class="rotated-text">$12.00</p>
            </div>
        </div>
        <button class="arrow right">&#10095;</button>
    </div>
    <script src="./scripts/menubest.js"></script>
</section>


        <section id="product-section" class="category">
    <?php foreach ($categories as $category): ?>
        <div class="group" id="<?= htmlspecialchars(strtolower($category['cname'])) ?>">
            <h3 class="title"><?= htmlspecialchars($category['cname']) ?></h3>
            <div class="product-showcase">
                <?php foreach ($category['products'] as $product): ?>
                    <div class="product-content">
                        <img src="<?= htmlspecialchars($product['productImage']) ?>" alt="<?= htmlspecialchars($product['pname']) ?>" />
                        <p class="rotated-text"><?= htmlspecialchars($product['pname']) ?><br><?= number_format($product['price']) ?> ¥</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="show-more-btn" data-group="<?= htmlspecialchars(strtolower($category['cname'])) ?>">Show More</button>
        </div>
    <?php endforeach; ?>
</section>


       </main>
    <footer>
        <script src="./scripts/menu.js"></script>
    </footer>
</body>
</html>
