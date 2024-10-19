<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');
include('./php/storeinfo.php');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
    <link rel="stylesheet" href="./styles/profile.css">
    <script src="./scripts/profile.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- アイコン -->
        <div class="avatar-container">
            <img id="avatar-preview" class="avatar" src="./images/" alt="アイコン">
            <label class="upload-button" for="avatar-input">
                <img src="upload-icon.png" alt="上傳">
            </label>
            <input type="file" id="avatar-input" accept="image/*">
        </div>
        <!-- form^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->
        <form class="edit-form" action="./php/storeProEditP.php" method="POST">
    <div class="form">
    <input type="hidden" name="userid" id="userid" value="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : ''; ?>">
    <label for="shop-name">店名</label>
<input type="text" id="shop-name" name="sname" value="<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>" required>


        <label for="address">住所</label>
        <input type="text" id="address" name="address"value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" required>

        <label for="phone">電話</label>
        <input type="text" id="phone" name="phone" value="<?php echo isset($_SESSION['tel']) ? htmlspecialchars($_SESSION['tel']) : ''; ?>" required >
    </div>
    
    <div class="save">
        <button type="submit" class="save-button">
            <img src="./images/signupBtn.png" alt="save">
        </button>
    </div>
</form>
    </div>
    <!-- loading -->

    <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center;">
        <div id="lottie"></div>
        </div>
        <script>
    // Lottie 起動
    document.addEventListener('DOMContentLoaded', function () {
// Lottie
const animation = lottie.loadAnimation({
container: document.getElementById('lottie'),
renderer: 'svg',
loop: true,
autoplay: true,
path: './images/loading.json' 
});

// animation
document.querySelector('.edit-form').addEventListener('submit', function (event) {
// 
event.preventDefault();
document.getElementById('loading').style.display = 'flex';

// set time animation
setTimeout(() => {
    this.submit();
}, 1500); 
});
});
</script>

        <!-- loading -->
</body>

</html>