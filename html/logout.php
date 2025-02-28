<?php
//セッションを利用可能にする
session_start();

// セッション情報を削除
$_SESSION = array();
session_destroy();

if($_SERVER['REQUEST_URI'] == '/logout.php?time_out') {
    echo '長時間操作がなかったのでログアウトしました';
} else {
    echo 'ログアウトが完了しました';
}
?>

<a href="login.php">ログインページに戻る</a>