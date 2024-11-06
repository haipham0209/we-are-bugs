<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/mainMgr.css">
    <title>商品管理</title>
</head>

<body>
    <header>
         <!-- Header navbar -->
         <div class="main-navbar">
            <div class="search-scan"> 
                <input type="text" class="search-bar" placeholder="Search...">
            </div>
            <button class="main-home">
                <h1 class="logo">WRB</h1>
            </button>
        </div>
    </header>
    <main>
        <div class="container">
            <!-- Tabs Section -->
            <div class="tabs">
                <button>Men</button>
                <button class="active">Women</button>
                <button>Children</button>
                <button>Special Events</button>
            </div>

            <!-- Product Cards -->
            <div class="product-card">
                <img src="product1.jpg" alt="Product Image">
                <div class="product-info">
                    <p><strong>名前：</strong>XXXXXXXX</p>
                    <p><strong>原価：</strong>XXXXXXXX</p>
                    <p><strong>値段：</strong>XXXXXXXX</p>
                    <p><strong>説明：</strong>XXXXXXXXXXXXXXXX</p>
                </div>
                <div class="stock">在庫: 2</div>
            </div>

            <div class="product-card">
                <img src="product2.jpg" alt="Product Image">
                <div class="product-info">
                    <p><strong>名前：</strong>XXXXXXXX</p>
                    <p><strong>原価：</strong>XXXXXXXX</p>
                    <p><strong>値段：</strong>XXXXXXXX</p>
                    <p><strong>説明：</strong>XXXXXXXXXXXXXXXX</p>
                </div>
                <div class="stock">在庫: 2</div>
            </div>

            <!-- Add Product Button -->
            <div class="add-product">
                <button>+</button>
            </div>
        </div>
    </main>
    <footer>    
    </footer>
</body>

</html>