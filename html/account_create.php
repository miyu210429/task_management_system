<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

if ($login_user['is_privileged'] !== 1) {
    header("Location: /acccount_list.php");
    exit();
}
    
//Userクラスをインスタンス化
$user = new User();

//ポストされた情報にエラーがないかチェック
if (!empty($_POST) ) {

    $array_mode['email']=true;
    $array_mode['login_name']=true;
    $array_mode['nickname']=true;
    $array_mode['password']=true;

    $validate_mode['email'] = true;
    $validate_mode['login_name'] = true;
    $validate_mode['nickname'] = true;

    $error_conditions = $user->validateInsertInput($_POST,$array_mode,$validate_mode);
    
    //ポストされた情報にエラーがなかったら
    //データベースに情報を挿入する
    if (empty($error_conditions)){

        $insert_array['email'] = $_POST['email'];
        $insert_array['login_name'] = $_POST['login_name'];
        $insert_array['nickname'] = $_POST['nickname'];
        $insert_array['password'] = $_POST['password'];

        if(empty($_POST['is_privileged'])){
            $insert_array['is_privileged'] = 0;
        } else {
            $insert_array['is_privileged'] = $_POST['is_privileged'];
        }
        
        $user->insert($insert_array);
        header('Location: account_list.php'); exit();
    }

}
?>

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
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <h1>アカウント登録</h1>
            <form action="" method="post" class="account-form">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="<?php if(isset($_POST['email']))
echo h($_POST['email']); ?>">
                <?php 
                if(isset($error_conditions['email']) && is_string($error_conditions['email'])) echo $error_conditions['email'];
                 ?>
            </div>
            
            <div class="form-group">
                <label for="login_name">ログイン名</label>
                <input type="text" id="login_name" name="login_name" value="<?php if(isset($_POST['login_name']))
echo h($_POST['login_name']); ?>">
            <?php 
            if(isset($error_conditions['login_name']) && is_string($error_conditions['login_name'])) echo $error_conditions['login_name'];
            ?>
            </div>

            <div class="form-group">
                <label for="nickname">ニックネーム(表示名)</label>
                <input type="text" id="nickname" name="nickname" value="<?php if(isset($_POST['nickname']))
echo h($_POST['nickname']); ?>">
            <?php 
            if(isset($error_conditions['nickname']) && is_string($error_conditions['login_name'])) echo $error_conditions['nickname'];
            ?>
            </div>
            
            <div class="form-group checkbox-group">
                <label for="is_privileged">
                <input type="checkbox" id="is_privileged" name="is_privileged" value="1" <?php if(isset($_POST['is_privileged'])): ?>checked<?php endif; ?>> 
                特権ユーザー（はいの場合チェック）
                </label>
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" value="<?php if(isset($_POST['password']))
echo h($_POST['password']); ?>">
            <?php 
            if(isset($error_conditions['password']) && is_string($error_conditions['password'])) echo $error_conditions['password'];
            ?>
            </div>
                <button type="submit">作成</button>
            </div>
            </form>
        </div>
    </main>



  </div>
</body>
</html>