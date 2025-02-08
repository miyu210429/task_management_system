<?php
session_start();

require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';

if(!empty($_POST)){
  
  $user = new User();
  $error = $user->validateLogin($_POST['login_name'],$_POST['password'],true);
  if(!$error){
    $logininfo = $user->login($_POST['login_name'],$_POST['password']);
    if($logininfo) {
      //$_SESSION['id']だと他にセッションでid持たせたいときにカニバリそうなので$_SESSION['User']['id']とする
      //削除済みのユーザーがログインできないようにチェックする
      $_SESSION['User']['id'] = $logininfo['id'];
      header('Location: task_list.php'); exit(); //ログイン成功時はタスク一覧ページへ
      }
    }

  /*
   * エラーの内容は詳細に出力可能だが、セキュリティの観点でなんでエラーなのかはシンプルにする
   * 登録系のフォームでは細かくエラー内容を出力する必要あり
   */
  $error_message = "ログインに失敗しました";  
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
    <?php if (isset($error_message) && !empty($error_message)) : ?>
      <div class="error-message">
        <?php echo h($error_message); ?>
      </div>
    <?php endif; ?>

    <form action="/login.php" method="post">
      <div class="form-group">
        <label for="username">ユーザー名</label>
        <input type="text" name="login_name" size="35" maxlength="225" value="<?php if(isset($_POST['login_name']))
echo h($_POST['login_name']); ?>" required>
      </div>
      <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" size="35" maxlength="225" value="<?php if(isset($_POST['password']))
echo h($_POST['password']); ?>" required>
      </div>
      <div class="form-group">
        <button type="submit">ログイン</button>
      </div>
    </form>
  </div>
</body>
</html>