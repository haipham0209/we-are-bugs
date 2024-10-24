<?php
session_start();

$servername = "localhost";
$username = "dbuser"; // Thay đổi theo thông tin của bạn
$password = "ecc"; // Thay đổi theo thông tin của bạn
$dbname = "wearebugs"; // Thay đổi theo thông tin của bạn

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Kiểm tra xem có tham số sname trong URL không
if (!isset($_GET['sname'])) {
    // Trả về mã trạng thái 404
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

// Nếu có tham số sname, tiếp tục xử lý
$storeName = $_GET['sname'];

// Khởi tạo các biến để tránh lỗi chưa khai báo
$tel = null;
$address = null;
$mail = null;
$sname = null;

// Thực hiện truy vấn để lấy dữ liệu cửa hàng và thông tin người dùng
$query = "SELECT store.sname, store.tel, store.address, user.mail FROM store 
          JOIN user ON store.userid = user.userid 
          WHERE store.sname = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $storeName); // Ràng buộc tham số
$stmt->execute();
$result = $stmt->get_result(); // Lấy kết quả truy vấn

// Kiểm tra nếu có dữ liệu cửa hàng
if ($result->num_rows > 0) {
    $storeData = $result->fetch_assoc(); // Lấy dữ liệu dưới dạng mảng kết hợp
    // Hiển thị dữ liệu cửa hàng
    $sname = $storeData["sname"];
    $tel = $storeData["tel"];
    $address = $storeData["address"];
    $mail = $storeData["mail"]; // Lấy email từ bảng user
} else {
    // Xử lý khi không tìm thấy cửa hàng
    header("HTTP/1.0 404 Not Found");
    exit();
}

// Đóng kết nối
$stmt->close();
$conn->close();
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
    
</head>
<body>
    <header>


        <nav class="nav-menu">
            <ul>
            <li><h3><?php echo $sname; ?></h3></li>
            <li><a href="./main.php?sname=<?php echo $_GET['sname']  ?>">ホームページ</a></li>
            <li><a href="./html/product.php">商品</a></li>
            <li><a href="./html/storeInfor.php">お店について</a></li>
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
    </header>

    <main>
                <!-- Navbar -->
                <div class="navbar">
        <button class="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
        </button>

        <div class="logo-container">
            <h1 class="logo">WRB</h1>
            <input type="text" class="search-bar" placeholder="Search...">
        </div>
        
        <button class="account" aria-label="Account button">
            <img src="./images/avataricon.jpg" alt="Account icon" class="account-icon">
        </button>
        

    </div>
        <div class="filter-buttons">
        <button class="filter-button active" data-target="#men">All</button>
            <button class="filter-button" data-target="#men">Men</button>
            <button class="filter-button" data-target="#women">Women</button>
            <button class="filter-button" data-target="#child">Children</button>
            <button class="filter-button">Special Events</button>
        </div>

        <section class="best-sellers">
            <h2>Best Sellers</h2>
            <div class="slider">
                <button class="arrow left">&#10094;</button>
                <div class="product-grid">
                    <div class="product">
                        <img src="./images/facebook.png" alt="Facebook" />
                    </div>
                    <div class="product">
                        <img src="./images/top_button.png" alt="Top Button" />
                    </div>
                    <div class="product">
                        <img src="./images/twitter.png" alt="Twitter" />
                    </div>
                    <div class="product">
                        <img src="./images/twitter.png" alt="Twitter" />
                    </div>
                    <div class="product">
                        <img src="./images/twitter.png" alt="Twitter" />
                    </div>
                </div>
                <button class="arrow right">&#10095;</button>
            </div>
            <script src="./scripts/menubest.js"></script>
        </section>

        <section id="product-section" class="category">
            <!-- Nhóm Men -->
            <div class="group" id="men">
                <h1 class="title">Men</h1>
                <div class="product-showcase">
                    <div class="product-content">
                        <img src="./images/product/1.jpg" alt="White Dress" />
                        <p class="rotated-text">White Dress<br>8000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/product/2.jpg" alt="Black Suit" />
                        <p class="rotated-text">Black Suit<br>12000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Blue Shirt" />
                        <p class="rotated-text">Blue Shirt<br>5000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Jacket" />
                        <p class="rotated-text">Jacket<br>15000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Jeans" />
                        <p class="rotated-text">Jeans<br>6000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="White Dress" />
                        <p class="rotated-text">White Dress<br>8000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Black Suit" />
                        <p class="rotated-text">Black Suit<br>12000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Blue Shirt" />
                        <p class="rotated-text">Blue Shirt<br>5000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Jacket" />
                        <p class="rotated-text">Jacket<br>15000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Jeans" />
                        <p class="rotated-text">Jeans<br>6000 ¥</p>
                    </div>
                </div>
                <button class="show-more-btn" data-group="men">Show More</button>
            </div>

            <!-- Nhóm Women -->
            <div class="group" id="women">
                <h1 class="title">Women</h1>
                <div class="product-showcase">
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Red Dress" />
                        <p class="rotated-text">Red Dress<br>9000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Green Top" />
                        <p class="rotated-text">Green Top<br>7000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Yellow Skirt" />
                        <p class="rotated-text">Yellow Skirt<br>7500 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Coat" />
                        <p class="rotated-text">Coat<br>10000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Scarf" />
                        <p class="rotated-text">Scarf<br>3000 ¥</p>
                    </div>
                </div>
                <button class="show-more-btn" data-group="women">Show More</button>
            </div>

            <!-- Nhóm Child -->
            <div class="group" id="child">
                <h1 class="title">Child</h1>
                <div class="product-showcase">
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="T-Shirt" />
                        <p class="rotated-text">T-Shirt<br>4000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Shorts" />
                        <p class="rotated-text">Shorts<br>3000 ¥</p>
                    </div>
                    <div class="product-content">
                        <img src="./images/facebook.png" alt="Cap" />
                        <p class="rotated-text">Cap<br>2000 ¥</p>
                    </div>
                </div>
                <button class="show-more-btn" data-group="child">Show More</button>
            </div>
        </section>
       </main>
    <footer>
        <script src="./scripts/menu.js"></script>
    </footer>
</body>
</html>
