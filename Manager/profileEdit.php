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
    <div class="form-row">
        <label for="shop-name">店名</label>
        <input type="text" id="shop-name" name="sname" value="<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>"<?php echo isset($_SESSION['sname']) && !empty($_SESSION['sname']) ? 'readonly' : ''; ?>  required>
        <img src=""class="icon">
    </div>
    <div class="form-row">
        <label for="address">住所</label>
                <input type="text" id="address" name="address"value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" readonly required>
                <img src="./images/pen.png" alt="編集" class="icon" onclick="toggleEdit('address')">
    </div>
    <div class="form-row">
        <label for="phone">電話</label>
        <input type="text" id="phone" name="phone" value="<?php echo isset($_SESSION['tel']) ? htmlspecialchars($_SESSION['tel']) : ''; ?>" readonly required >
        <img src="./images/pen.png" alt="編集" class="icon" onclick="toggleEdit('phone')">
    </div>
    <div class="save">
        <button type="submit" class="save-button">
            <img src="./images/signupBtn.png" alt="save">
        </button>
    </div>
</div>
    <script>
        function toggleEdit(fieldId) {
            var field = document.getElementById(fieldId);
            field.readOnly = !field.readOnly;
            field.focus();
        }
    </script>

    <!-- Link to open the dialog -->
    <div>
        <span class="edit-password-link" onclick="openDialog()">パスワードを編集</span>
    </div>
    <!-- Password Change Modal -->
<!-- Dialog password không yêu cầu trong form chính -->
<div id="passwordDialog" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" onclick="closeDialog()">&times;</span>
        <h2>パスワードを変更</h2>
        <form action="./php/changePassword.php" method="POST">
            <div>現在のパスワード</div>
            <input type="password" id="old-password" name="old_password">

            <div>新しいパスワード</div>
            <input type="password" id="new-password" name="new_password">

            <div>新しいパスワード（確認）</div>
            <input type="password" id="confirm-password" name="confirm_password">

            <button type="submit" class="confirm-btn">確認</button>
        </form>
    </div>
</div>


<div>
    <a href="../main.php?sname=<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>" target="_blank" rel="noopener noreferrer">
        ストアのリンクアドレス
    </a>
    <!-- Copy Icon -->
    <img src="./images/copy.png" alt="Copy Link" class="copy-icon" onclick="copyLink()" style="cursor: pointer; width: 20px; height: 20px; margin-left: 8px;">
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
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='main.php'">
        </a>
    </footer>

</html>