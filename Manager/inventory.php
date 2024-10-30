<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/inventory.css">
    <title>在庫</title>
</head>
<body>
    <div class="container">
        <h1>在庫管理</h1>
        <input type="text" id="search" placeholder="商品を探" onkeyup="searchProducts()">
        <div class="table-container">
            <table id="productTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>商品コード</th>
                        <th>商品名</th>
                        <th>在庫</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Thêm nhiều sản phẩm hơn nếu cần -->
                    <script>
                        for (let i = 1; i <= 100; i++) {
                            document.write('<tr><td>' + i + '</td><td>SP00' + i + '</td><td>Sản phẩm ' + i + '</td><td>' + (i * 10) + '</td></tr>');
                        }
                    </script>
                </tbody>
            </table>
        </div>
    </div>
    <script src="./scripts/inventory.js"></script>
</body>
<footer style="text-align: center">
        <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='main.php'">
        </a>
    </footer>
</html>