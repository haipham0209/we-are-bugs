<?php
include('auth_check.php');
include('db_connect.php');

$productid = $_POST['productid'];
$storeid = $_SESSION['storeid'];

// Thực hiện câu lệnh UPDATE mà không thay đổi barcode và các trường khác
$sql = "UPDATE product SET price = ?, costPrice = ?, description = ?, stock_quantity = ? WHERE productid = ? AND storeid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ddsiii", $_POST['price'], $_POST['costPrice'], $_POST['description'], $_POST['stockQuantity'], $productid, $storeid);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Nếu cập nhật thành công, chuyển hướng về trang product.php
    header("Location: ../product.php?status=success");
    exit();
} else {
    echo "Cập nhật không thành công.";
}

$stmt->close();
$conn->close();
?>

<!-- <?php
include('auth_check.php');
include('db_connect.php');

$productid = $_POST['productid'];
$storeid = $_SESSION['storeid'];

// Kiểm tra xem người dùng có tải ảnh lên không
$productImage = isset($_FILES['productImage']) && $_FILES['productImage']['error'] == UPLOAD_ERR_OK 
    ? $_FILES['../' .$product['productImage']] // Nếu có ảnh mới, lấy tên file ảnh
    : $_POST['currentProductImage'];  // Nếu không có ảnh mới, giữ lại ảnh cũ

// Thực hiện câu lệnh UPDATE mà không thay đổi barcode
$sql = "UPDATE product SET pname = ?, price = ?, costPrice = ?, description = ?, stock_quantity = ?, category_id = ?, productImage = ? WHERE productid = ? AND storeid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sddsiiiii", $_POST['pname'], $_POST['price'], $_POST['costPrice'], $_POST['description'], $_POST['stockQuantity'], $_POST['category'], $productImage, $productid, $storeid);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Nếu cập nhật thành công, chuyển hướng về trang product.php
    header("Location: ../product.php?path= " .urlencode($productImage));
    exit();
} else {
    echo "Cập nhật không thành công.";
}

$stmt->close();
$conn->close();

?> -->
