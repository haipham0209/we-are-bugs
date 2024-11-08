<?php
include('./php/auth_check.php');
include('./php/db_connect.php');

$productid = $_GET['id'] ?? null;

if ($productid) {
    $sql = "DELETE FROM product WHERE productid = ? AND storeid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productid, $_SESSION['storeid']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Xóa thành công, chuyển hướng về trang product.php
        header("Location: product.php");
    } else {
        echo "Không thể xóa sản phẩm.";
    }
    $stmt->close();
} else {
    echo "ID sản phẩm không hợp lệ.";
}
$conn->close();
?>
