<?php
include('auth_check.php');
include('db_connect.php');

$productid = $_POST['productid'];
$storeid = $_SESSION['storeid'];
$category = $_POST['category'];

// Lưu lại category_id cũ trước khi thay đổi
$old_category_sql = "SELECT category_id FROM product WHERE productid = ? AND storeid = ?";
$old_stmt = $conn->prepare($old_category_sql);
$old_stmt->bind_param("ii", $productid, $storeid);
$old_stmt->execute();
$old_stmt->bind_result($old_category_id);
$old_stmt->fetch();
$old_stmt->close();

// Cập nhật sản phẩm với các trường được phép thay đổi
$sql = "UPDATE product SET price = ?, costPrice = ?, description = ?, stock_quantity = ?, category_id = ? WHERE productid = ? AND storeid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ddsiiii", $_POST['price'], $_POST['costPrice'], $_POST['description'], $_POST['stockQuantity'], $category, $productid, $storeid);
$stmt->execute();

// Kiểm tra và xóa danh mục nếu cần
if ($stmt->affected_rows > 0) {
    // Kiểm tra nếu category cũ không còn sản phẩm nào
    $check_category_sql = "SELECT COUNT(*) FROM product WHERE category_id = ?";
    $check_stmt = $conn->prepare($check_category_sql);
    $check_stmt->bind_param("i", $old_category_id);
    $check_stmt->execute();
    $check_stmt->bind_result($product_count);
    $check_stmt->fetch();
    $check_stmt->close();

    // Nếu không còn sản phẩm nào, xóa category cũ
    if ($product_count == 0) {
        $delete_category_sql = "DELETE FROM category WHERE category_id = ?";
        $delete_stmt = $conn->prepare($delete_category_sql);
        $delete_stmt->bind_param("i", $old_category_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    // Chuyển hướng sau khi cập nhật thành công
    header("Location: ../product.php?status=success");
    exit();
} else {
    // echo "Cập nhật không thành công.";
    header("Location: ../error.php");
}

$stmt->close();
$conn->close();
?>


