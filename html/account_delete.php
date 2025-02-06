<?php
//セッションを利用可能にする
session_start();

require_once '../app/config.php';
require_once '../app/autoload.php';

//ログインしているかチェック
if (empty($_SESSION['User']['id'])){
    header('Location: account_list.php');echo  exit();
}

//Userクラスをインスタンス化する
$user = new User() ;

//リクエストのidが存在しているユーザーかどうかチェック
if ($user->getById($_REQUEST['id']) === false ) {
    header('Location: account_list.php');echo  exit();
} else {
    $delete_user = $user->getById($_REQUEST['id']);
}

//削除する側の情報を取ってきて、特権ユーザーであるかをチェックする
$login_user = $user->getById($_SESSION['User']['id']);

if (!isset($_REQUEST['id']) || $login_user['is_privileged'] !== 1){
    header('Location: account_list.php');echo  exit();
}


//ゲットクエリにあるidからデータベースis_deletedに情報を追加する
$user->deleteUser($_REQUEST['id']);

//すでに削除されているユーザーかどうかチェック
//削除が完了したことを表示
if ($delete_user['is_deleted'] === 1) {
    echo 'そのユーザーは削除されています<br />';
} else {
     echo '削除が完了しました';
}

?>

<a href="account_list.php">戻る</a>

