<?php
session_start();
try {
    $db = new PDO('mysql:dbname=task_management;host=mysql;charset=utf8', 'akinori','qazWSX098');
}   catch(PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
}

if(!empty($_POST)){
  $login = $db->prepare('SELECT * FROM users WHERE login_name=? AND password=?');
  $login->execute(array(
    $_POST['login_name'],
    sha1($_POST['password']."qaz")
    ));
  $logininfo = $login->fetch();

  $_SESSION['id'] = $logininfo['id'];
    
    header('Location: task_list.php'); exit();
  }
?>
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
        <input type="text" name="login_name" size="35" maxlength="225" value="<?php if(isset($_POST['login_name']))
echo htmlspecialchars($_POST['login_name'],ENT_QUOTES); ?>" required>
      </div>
      <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" size="35" maxlength="225" value="<?php if(isset($_POST['password']))
echo htmlspecialchars($_POST['password'],ENT_QUOTES); ?>" required>
      </div>
      <div class="form-group">
        <button type="submit">ログイン</button>
      </div>
    </form>
  </div>
</body>
</html>