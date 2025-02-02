<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アカウント登録</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/account.css">
</head>
<body>
  <div class="container">
    <?php include './template/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <h1>アカウント登録</h1>
            <form action="/" method="post" class="account-form">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="username">ログイン名</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="nickname">ニックネーム(表示名)</label>
                <input type="text" id="nickname" name="nickname" required>
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
                <button type="submit">作成</button>
            </div>
            </form>
        </div>
    </main>



  </div>
</body>
</html>