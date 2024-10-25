

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - My Page</title>
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="../styles/myPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    <header>
        <!-- Navbar -->
        <div class="navbar">
            <button class="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
            </button>
            <div class="logobar">
                <img src="../images/wrb-logo.png" alt="Store Logo">
            </div>
            <button class="avatar-toggle">
                <img class="avatar" src="../images/avataricon.jpg" alt="Avatar User">
            </button>
        </div>

    <nav class="nav-menu">
        <ul>
        <li><h3><?php echo $sname; ?></h3></li>
          <li><a href="./main.php?sname=<?php echo $_GET['sname']  ?>">ホームページ</a></li>
          <li><a href="./html/product.php">商品</a></li>
          <li><a href="./html/storeInfor.php">お店について</a></li>
          <li class="support-title">サポート</li>
          <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo $tel; ?>"><?php echo $tel; ?></a></li>
          <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mail:"><?php echo $mail; ?></a></li>
          <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a></li>
         </ul>
    </nav>
    <nav class="nav-myPage">
        <ul>
            <li><a href="#">登録</a></li>
            <li><a href="#">ログイン</a></li>
        </ul>
    </nav>
       <div class="overlay"></div>
    </header>

    <main>
        <div class="mypage-container">
            <!-- Order History Section -->
            <div class="order-history">
                <h2>
                    <img src="../images/time.png" alt="History Icon" class="icon"> 注文履歴
                </h2>
                <div class="order-items">
                    <img src="../images/product/1.jpg" alt="Order Item">
                </div>
            </div>
            <!-- Shopping Cart Section -->
            <div class="cart">
                <h2>
                    <img src="../images/shopping.png" alt="Cart Icon" class="icon"> 買い物カゴ
                </h2>
                <div class="cart-items">
                    <div class="cart-img">
                        <img src="../images/no-image.png" alt="Cart Item">
                    </div>
                    <div class="cart-info">
                        <div class="cart-details">
                            <p>1アイテム</p>
                            <p>1,500¥</p>
                        </div>
                        <button class="buy-button">ご購入</button>
                    </div>
                </div>
            </div>
            <!-- Favorites Section -->
            <div class="favorites">
                <h2>
                    <img src="../images/black-heart.png" alt="Favor Icon" class="icon"> お気に入りリスト
                </h2>
                <div class="favor-items">
                    <img src="../images/no-image.png" alt="Favor Item">
                    <img src="../images/no-image.png" alt="Favor Item">
                    <img src="../images/no-image.png" alt="Favor Item">
                </div>
            </div>
        </div>
    </main>
    <footer>
       
    </footer>

</body>
<script src="../scripts/menu.js"></script>
<script src="../scripts/mypage.js"></script>
</html>
