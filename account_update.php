<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アカウント作成</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/account.css">
</head>
<body>
  <div class="container">
    <?php include './template/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <h1>アカウント編集</h1>
            <p class="account-info">美優さんのアカウント</p>
            <form action="/" method="post" class="account-form">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="username">ユーザー名</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group checkbox-group">
                <label for="is_admin">
                <input type="checkbox" id="is_admin" name="is_admin" value="1">
                特権ユーザー（はいの場合チェック）
                </label>
            </div>
            
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit">更新</button>
            </div>
            </form>
        </div>
    </main>



  </div>
</body>
</html>