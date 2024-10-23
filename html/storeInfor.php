
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - Store Information</title>
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="../styles/storeInfor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    <header>
        <!-- Navbar -->
        <div class="navbar">
            <button class="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
            </button>
            <input type="text" class="search-bar" placeholder="Search...">
            <h1 class="logo">WRB</h1>
        </div>

    <nav class="nav-menu">
        <ul>
        <li><h3><?php echo $sname; ?></h3></li>
          <li><a href="./main.php?sname=<?php echo $_GET['sname']  ?>">ホームページ</a></li>
          <li><a href="./html/product.php">商品</a></li>
          <li><a href="./html/storeInfor.php">お店について</a></li>
          <li><a href="#">会員登録</a></li>
          <li><a href="#">ログイン</a></li>
          <li class="support-title">サポート</li>
          <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo $tel; ?>"><?php echo $tel; ?></a></li>

          <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mail:"><?php echo $mail; ?></a></li>
          <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a></li>
         </ul>
    </nav>
       <div class="overlay"></div>
    </header>

    <main>
        <!-- Store Information Section -->
        <div class="store-info">
            <!-- Logo Section -->
            <div class="logo">
                <img src="../images/wrb-logo.png" alt="WRB Logo">
            </div>
             <!-- About Store Section -->
            <div class="about-store">
                <h2>店舗紹介</h2>
                <p>私たちのショップは、大阪にある若者向けのファッションを専門に提供するアパレル店です。最新のトレンドを取り入れたデザインで、10代から若者まで多くのお客様にご愛顧いただいております。</p>

                <h2>所在地</h2>
                <p>大阪府〇〇町〇〇</p>

                <h2>電話番号</h2>
                <p>📞+81 90 0000 0000</p>

                <h2>お客様の声</h2>
                <p>「トレンドを押さえたセンスの良い商品がたくさんあって、お気に入りです！」</p>
                <p>「スタッフが親切で、親身になってアドバイスしてくれるのが嬉しいです。」</p>
            </div>
           
        </div>

        
    </main>
    <footer>
        <!-- Social Media Section -->
        <div class="social-media">
            <a href="#"><img src="../images/twitter.png" alt="Twitter"></a>
            <a href="#"><img src="../images/facebook.png" alt="Facebook"></a>
            <a href="#"><img src="../images/instagram.png" alt="Instagram"></a>
        </div>
    </footer>

</body>
<script src="../scripts/menu.js"></script>

</html>
