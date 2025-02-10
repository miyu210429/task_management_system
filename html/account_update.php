<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

if ($login_user['is_privileged'] !== 1 && $_SESSION['User']['id'] != $_REQUEST['id']) {
    header("Location: /account_list.php");
    exit();
}

//Userクラスをインスタンス化する
$user = new User();

//編集される人の情報を取ってくる
$update_user = $user->getById($_REQUEST['id']);

//ポストされていたらエラーの確認
if(!empty($_POST)) {

    $error_conditions = $user->validateUpdateInput($_POST, $update_user);

    //エラーがなければポストの情報を配列に入れてUPDATEする
    if(empty($error_conditions)) {

        $update_array['email'] = $_POST['email'];
        $update_array['nickname'] = $_POST['nickname'];

        if(empty($_POST['is_privileged'])){
            $update_array['is_privileged'] = $update_user['is_privileged'];
        } else {
            $update_array['is_privileged'] = $_POST['is_privileged'];
        }
        $update_array['id'] = (int) $_REQUEST['id'];

        $user->update($update_array);
         
        header("Location: /account_list.php") ;exit();
    }

}

?>
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
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <h1>アカウント編集</h1>
            <p class="account-info"><?php echo $update_user['login_name'] ?>さんのアカウント</p>
            <form action="" method="post" class="account-form">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="<?php if(isset($_POST['email']))
echo h($_POST['email']); ?>">
            <?php
            if(isset($error_conditions['email']) && is_string($error_conditions['email'])) echo $error_conditions['email']
            ?>
            </div>
            

            <div class="form-group">
                <label for="nickname">ニックネーム(表示名)</label>
                <input type="text" id="nickname" name="nickname" value="<?php if(isset($_POST['nickname']))
echo h($_POST['nickname']); ?>">
            <?php
            if(isset($error_conditions['nickname']) && is_string($error_conditions['nickname'])) echo $error_conditions['nickname']
            ?>
            </div>
            
            <div class="form-group checkbox-group">
            <?php  
            if($login_user['is_privileged'] === 1 && $login_user['id'] != $_REQUEST['id'] ) : ?>
                <label for="is_privileged">
                <input type="checkbox" id="is_privileged" name="is_privileged" value="1">
                特権ユーザー（はいの場合チェック）
                </label>
            <?php endif ?>
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