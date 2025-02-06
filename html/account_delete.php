<?php
//セッションを利用可能にする
session_start();

require_once '../app/config.php';
require_once '../app/autoload.php';

//Userクラスをインスタンス化する
$delete = new User() ;

//削除する側の情報を取ってきて、特権ユーザーであるかを確認する
$login_user = $delete->getById($_SESSION['User']['id']);

if (!empty($_SESSION['User']['id'])){
    if (!isset($_REQUEST['id']) || $login_user['is_privileged'] !== 1){
    header('Location: account_list.php');echo  exit();
    }
}

//ゲットクエリにあるidからデータベースis_deletedに情報を追加する
$delete->deleteUser($_REQUEST['id']);

//削除が完了したことを表示
echo '削除が完了しました';
?>

<a href="account_list.php">戻る</a>

