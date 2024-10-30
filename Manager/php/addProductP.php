<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "dbuser";
$password = "ecc";
$dbname = "wearebugs";
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Giả sử thông tin username đã có trong session
$username = $_SESSION['username'];
$userid = $_SESSION['userid']; // Lấy userid của người dùng hiện tại từ session

// Lấy danh sách category của người dùng
$categories_sql = "SELECT * FROM category WHERE userid = ?";
$stmt = $conn->prepare($categories_sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$categories_result = $stmt->get_result();

// Kiểm tra nếu có sản phẩm được thêm vào
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xác thực dữ liệu đầu vào từ form
    $category_id = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
    $pname = trim($_POST['pname']);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $costPrice = filter_input(INPUT_POST, 'costPrice', FILTER_VALIDATE_FLOAT);
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $stockQuantity = filter_input(INPUT_POST, 'stockQuantity', FILTER_VALIDATE_INT);
    $barcode = trim($_POST['barcode']);

    // Kiểm tra nếu có lỗi tải lên ảnh
    if ($_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
        echo "Lỗi khi tải lên ảnh!";
        exit();
    }

    // Tạo tên file duy nhất để lưu ảnh
    $imageExtension = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
    $uniqueImageName = $userid . "_" . time() . "." . $imageExtension;
    $user_folder = "../storeproductImg/" . $username;
    $category_folder = $user_folder . "/" . $category_id;
    $imagePath = $category_folder . "/" . $uniqueImageName;

    // Tìm `productid` tiếp theo cho người dùng hiện tại
    $stmt = $conn->prepare("SELECT IFNULL(MAX(productid), 0) + 1 AS next_productid FROM product WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($next_productid);
    $stmt->fetch();
    $stmt->close();

    // Thêm sản phẩm vào bảng product trước, nhưng chưa tải lên ảnh
    $stmt = $conn->prepare("INSERT INTO product (productid, userid, category_id, pname, price, costPrice, description, stock_quantity, barcode, productImage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissssiss", $next_productid, $userid, $category_id, $pname, $price, $costPrice, $description, $stockQuantity, $barcode, $imagePath);

    if ($stmt->execute()) {
        // Nếu thêm sản phẩm thành công, tạo thư mục và lưu ảnh vào thư mục
        if (!file_exists($category_folder)) {
            mkdir($category_folder, 0777, true);
        }

        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $imagePath)) {
            echo "Sản phẩm đã được thêm thành công!";
        } else {
            echo "Lỗi khi lưu ảnh!";
            // Nếu lưu ảnh thất bại, xoá sản phẩm vừa thêm khỏi cơ sở dữ liệu
            $delete_stmt = $conn->prepare("DELETE FROM product WHERE productid = ? AND userid = ?");
            $delete_stmt->bind_param("ii", $next_productid, $userid);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    } else {
        echo "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.";
    }

    $stmt->close();
}

$conn->close();
?>
