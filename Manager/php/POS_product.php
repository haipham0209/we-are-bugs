<?php

// require_once('./db_connect.php');

// Hàm lấy thông tin sản phẩm qua barcode
function getProductByBarcode($conn, $barcode, $storeid) {
    $sql = "SELECT pname, price, stock_quantity, productid FROM product WHERE barcode = ? AND storeid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $barcode, $storeid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Hàm cập nhật số lượng tồn kho
function updateProductStock($conn, $productid, $quantity) {
    $sql = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE productid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $productid);
    return $stmt->execute();
}
?>
