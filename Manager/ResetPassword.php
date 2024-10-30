<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
    <title>新しいパスワード設定</title>
    <link rel="stylesheet" href="./styles/StoreLogin.css">
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>WRB</h1>
            <p>～Set New Password～</p>
        </div>
        <div class="login-form">
            <h2>新しいパスワードを入力</h2>
            <form action="./php/ProcessResetPassword.php" method="POST">
                <input type="hidden" name="username" value="<?= htmlspecialchars($_GET['username']) ?>">

                <p>新しいパスワード</p>
                <input type="password" name="new_password" placeholder="新しいパスワードを入力" required>

                <p>パスワード確認</p>
                <input type="password" name="confirm_password" placeholder="もう一度パスワードを入力" required>

                <button type="submit">パスワードをリセット</button>
            </form>
        </div>
    </div>
</body>

</html>