<?php
// Kết nối cơ sở dữ liệu
include('./Manager/php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Tạo mảng resources để lưu trữ sản phẩm theo category_id
$resources = [];

// Truy vấn sản phẩm và thông tin danh mục
$query = "
    SELECT p.productid, p.pname, p.price, p.productImage, p.description, 
           c.category_id, c.cname
    FROM product p
    JOIN category c ON p.category_id = c.category_id
    WHERE p.storeid = 1
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoryId = $row['category_id'];
        $categoryName = $row['cname'];

        // Kiểm tra nếu danh mục chưa có trong resources, tạo mới
        if (!isset($resources[$categoryId])) {
            $resources[$categoryId] = [
                'cname' => $categoryName,
                'products' => []
            ];
        }

        // Thêm sản phẩm vào danh mục tương ứng
        $resources[$categoryId]['products'][] = [
            'productid' => $row['productid'],
            'pname' => $row['pname'],
            'price' => $row['price'],
            'productImage' => $row['productImage'],
            'description' => $row['description']
        ];
    }
}

// Đóng kết nối
$conn->close();

// In ra mảng resources để kiểm tra
// echo "<pre>";
// print_r($resources);
// echo "</pre>";
?>
