<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';

//Userクラスをインスタンス化
$user = new User();

//ポストされた情報にエラーがないかチェック
if (!empty($_POST) ) {
    //メールアドレス
    $new_email = $user->validateEmail($_POST['email']);
    if (isset($_POST['email']) && $_POST['email'] == '') {
        $error['email'] = 'blank';
    }

    if (is_string($new_email)) {
        $error_conditions['email'] = $new_email;
    }

    //ログイン名
    $new_login_name = $user->validateLoginName($_POST['login_name']);
    if (isset($_POST['login_name']) && $_POST['login_name'] == ''){
        $error['login_name'] = 'blank';
    }

    if (is_string($new_login_name)) {
        $error_conditions['login_name'] = $new_login_name;
    }
    
    //ニックネーム
    $new_nickname = $user->validateNickname($_POST['nickname']);
    if (isset($_POST['nickname']) && $_POST['nickname'] == '') {
        $error['nickname'] = 'blank';
    }

    if (is_string($new_nickname)){
        $error_conditions['nickname'] = $new_nickname;
    }

    //特権にチェックされたら１、なければ０
    $_POST['is_privileged'] = 0;

    //パスワード
    $new_password = $user->validatePassword($_POST['password']);
    if (isset($_POST['password']) && $_POST['password'] == '') {
        $error['password'] = 'blank';
    }

    if(is_string($new_password)) {
        $error_conditions['password'] = $new_password;
    }
}


//ポストされた情報にエラーがなかったら
//データベースに情報を挿入する

if(!empty($_POST)) {
    if (empty($error) && !isset($error_conditions)){

        $insert_array['email'] = $_POST['email'];
        $insert_array['login_name'] = $_POST['login_name'];
        $insert_array['nickname'] = $_POST['nickname'];
        $insert_array['password'] = $_POST['password'];
        $insert_array['is_privileged'] = $_POST['is_privileged'];

        $new_user = $user->getCreateUser($insert_array);
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
                if(!empty($error['email'])) echo '入力してください';
                if(isset($error_conditions['email'])) echo $error_conditions['email'];
                 ?>
            </div>
            
            <div class="form-group">
                <label for="login_name">ログイン名</label>
                <input type="text" id="login_name" name="login_name" value="<?php if(isset($_POST['login_name']))
echo h($_POST['login_name']); ?>">
            <?php 
            if(!empty($error['login_name'])) echo '入力してください' ;
            if(isset($error_conditions['login_name'])) echo $error_conditions['login_name'];
            ?>
            </div>

            <div class="form-group">
                <label for="nickname">ニックネーム(表示名)</label>
                <input type="text" id="nickname" name="nickname" value="<?php if(isset($_POST['nickname']))
echo h($_POST['nickname']); ?>">
            <?php 
            if(!empty($error['nickname'])) echo '入力してください'  ;
            if(isset($error_conditions['nickname'])) echo $error_conditions['nickname'];
            ?>
            </div>
            
            <div class="form-group checkbox-group">
                <label for="is_privileged">
                <input type="checkbox" id="is_privileged" name="is_privileged" value="1">
                特権ユーザー（はいの場合チェック）
                </label>
            </div>
            
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" value="<?php if(isset($_POST['password']))
echo h($_POST['password']); ?>">
            <?php 
            if(!empty($error['password'])) echo '入力してください'  ;
            if(isset($error_conditions['password'])) echo $error_conditions['password'];
            
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