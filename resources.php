<?php
// Kết nối cơ sở dữ liệu
include('./Manager/php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Tạo mảng categories để lưu trữ sản phẩm theo category
$categories = [];

// Truy vấn sản phẩm và thông tin danh mục
$query = "
    SELECT p.productid, p.pname, p.price, p.productImage, 
           c.cname
    FROM product p
    JOIN category c ON p.category_id = c.category_id
    WHERE p.storeid = 1
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoryName = $row['cname'];

        // Kiểm tra nếu danh mục chưa có trong categories, tạo mới
        if (!isset($categories[$categoryName])) {
            $categories[$categoryName] = [
                'cname' => $categoryName,
                'products' => []
            ];
        }

        // Thêm sản phẩm vào danh mục tương ứng
        $categories[$categoryName]['products'][] = [
            'productid' => $row['productid'],
            'pname' => $row['pname'],
            'price' => $row['price'],
            'productImage' => $row['productImage']
        ];
    }
}

// Đóng kết nối
$conn->close();

// Định dạng mảng cuối cùng
$categories = array_values($categories); // Chuyển về dạng mảng chỉ số

// In ra mảng categories để kiểm tra
// // echo "<pre>";
// print_r($categories);
// echo "</pre>";
?>
