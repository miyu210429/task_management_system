<?php
require_once '../app/config.php';
require_once '../app/autoload.php';
 //ログインチェック、ログインできていなければログインページに遷移できていれば$login_userにログイン者の情報が入る
 require_once '../app/auth.php';


//Userクラスをインスタンス化する
$user = new User() ;

//リクエストのidが存在しているユーザーかどうかチェック
$delete_target = $user->getById($_REQUEST['id']);
if (!$delete_target) {
    header('Location: account_list.php');echo  exit();
}

if (!isset($_REQUEST['id']) || $login_user['is_privileged'] !== 1){
    header('Location: account_list.php');echo  exit();
}


//ゲットクエリにあるidからデータベースis_deletedに情報を追加する
$user->deleteUser($_REQUEST['id']);

//すでに削除されているユーザーかどうかチェック
//削除が完了したことを表示
if ($delete_target['is_deleted'] === 1) {
    echo 'そのユーザーは削除されています<br />';
}   
echo '削除が完了しました';


?>

<a href="account_list.php">戻る</a>

