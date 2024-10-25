<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');
include('./php/storeinfo.php');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>プロフィール</title>
     <link rel="stylesheet" href="./styles/profileEdit.css">
    <script src="./scripts/profile.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- アイコン -->
        <div class="avatar">
        <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="loadImage(event)">
            <svg width="198" height="107" viewBox="0 0 198 107" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="99" cy="53.5" rx="99" ry="53.5" fill="#B0D9B1"/>
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="20" fill="#333">ロゴ を選び</text>
            </svg>
            <label class="upload-button" for="fileInput">
            <img src="upload-icon.png" alt="ロゴマーク">
        </label>
            <!-- <img id="avatar-preview" class="avatar" src="./images/" alt="アイコン"> -->
            <!-- <label class="upload-button" for="avatar-input">
                <img src="upload-icon.png" alt="ロゴマーク">
            </label>
            <input type="file" id="avatar-input" accept="image/*"> -->
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
    <div>
    <a href="../main.php?sname=<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>" target="_blank" rel="noopener noreferrer">
        ストアのリンクアドレス
    </a>
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
<footer style="text-align: center">
        <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;">
        </a>
    </footer>

</html>