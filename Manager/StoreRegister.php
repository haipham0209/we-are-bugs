<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/StoreRegister.css">
    <title>登録</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>

    <script>
        //local storage check
        if (localStorage.getItem('registerSuccess')) {
            localStorage.removeItem('registerSuccess');
            setTimeout(function() {
                window.location.href = './StoreLogin.php'; //loginpage 移動
            }, 0);
        }
    </script>
    <div class="container">
        <!-- ロゴ部分 -->
        <div class="logo">
            <h1>WRB</h1>
            <p>～Fashion & Boutique～</p>
        </div>

        <form class="register-form" action="./php/register.php" method="post">
            <h2>Register</h2>

            <label for="username"></label>
            <?php if (isset($_GET['error']) && $_GET['error'] == 'username_exists'): ?>
                <span style="color: red;">ユーザー名がすでに存在します！</span>
            <?php endif; ?>
            <p>ユーザー名</p>
            <input type="text" id="username" name="username" placeholder="ユーザー名入力" required value="<?= isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '' ?>">

            <?php if (isset($_GET['error']) && $_GET['error'] == 'sname_exists'): ?>
                <span style="color: red;">店名が既に存在します！</span>
            <?php endif; ?>
            <label for="sname"></label>
            <input type="text" id="sname" name="sname" placeholder="店名を入力" required value="<?= isset($_GET['sname']) ? htmlspecialchars($_GET['sname']) : '' ?>">

            <?php if (isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
                <span style="color: red;">既に登録しているメールです！</span>
            <?php endif; ?>
            <label for="email"></label>
            <input type="email" id="email" name="email" placeholder="メールを入力" required value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>">

            <?php if (isset($_GET['error']) && $_GET['error'] == 'password_too_short'): ?>
                <span style="color: red;">パスワードは6文字以上でなければなりません！</span>
            <?php endif; ?>
            <label for="password"></label>
            <input type="password" id="password" name="password" placeholder="パスワード" required>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'password_mismatch'): ?>
                <span style="color: red;">パスワードが一致しません！</span>
            <?php endif; ?>

            <label for="confirm_password"></label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="もう一回パスワード" required>


            <button type="submit">登録</button>
        </form>

        <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center;">
            <div id="lottie"></div>
        </div>
        <script>
            // Lottie 起動
            document.addEventListener('DOMContentLoaded', function() {
                // Lottie
                const animation = lottie.loadAnimation({
                    container: document.getElementById('lottie'),
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: './images/loading.json'
                });

                // animation
                document.querySelector('.register-form').addEventListener('submit', function(event) {
                    // 
                    event.preventDefault();
                    document.getElementById('loading').style.display = 'flex';

                    // set time animation
                    setTimeout(() => {
                        this.submit();
                    }, 2500);
                });
            });
        </script>
        <?php
        if (isset($_GET['success']) && $_GET['success'] === 'true') {
            // cache 
            header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
            header("Pragma: no-cache"); // HTTP 1.0.
            header("Expires: 0"); // 
            header('Location: ./StoreLogin.html');

            exit;
        }
        ?>
    </div>
</body>

</html>