<!-- login.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン - タスク管理システム</title>
  <link rel="stylesheet" href="./css/login.css">
</head>
<body>
  <div class="login-container">
    <h1>タスク管理システム ログイン</h1>
    <form action="" method="post">
      <div class="form-group">
        <label for="username">ユーザー名</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <button type="submit">ログイン</button>
      </div>
    </form>
  </div>
</body>
</html>