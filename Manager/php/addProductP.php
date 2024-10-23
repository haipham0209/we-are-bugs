<?php
// Bước 1: Kết nối với cơ sở dữ liệu
$servername = "localhost"; // Thay đổi theo cấu hình của bạn
$username = "dbuser"; // Thay đổi theo cấu hình của bạn
$password = "ecc"; // Thay đổi theo cấu hình của bạn
$dbname = "wearebugs"; // Thay đổi theo cấu hình của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Bước 2: Nhận dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productImage = $_FILES['productImage'];
    $categoryText = $_POST['categoryText'];
    $pname = $_POST['pname'];
    $price = $_POST['price'];
    $costPrice = $_POST['costPrice'];
    $description = $_POST['description'];
    $stockQuantity = $_POST['stockQuantity'];
    $barcode = $_POST['barcode'];

    // Bước 3: Xử lý hình ảnh (nếu cần)
    // Lưu hình ảnh vào thư mục mong muốn và lấy đường dẫn
    $imagePath = "./storeproductImg/" . basename($productImage["name"]);
    move_uploaded_file($productImage["tmp_name"], $imagePath);

    // Bước 4: Lấy category_id từ bảng category
    $stmt = $conn->prepare("SELECT category_id FROM category WHERE cname = ?");
    $stmt->bind_param("s", $categoryText);
    $stmt->execute();
    $stmt->bind_result($category_id);
    $stmt->fetch();
    $stmt->close();

    // Bước 5: Ghi dữ liệu vào bảng product
    $stmt = $conn->prepare("INSERT INTO product (productImage, category_id, pname, price, costPrice, description, stockQuantity, barcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssis", $imagePath, $category_id, $pname, $price, $costPrice, $description, $stockQuantity, $barcode);
    $stmt->execute();
    $stmt->close();

    echo "Product added successfully!";
}else{
    echo"WWWWWWWWWWWWWWWWWWWWWWWw";
}

// Đóng kết nối
$conn->close();
?>
