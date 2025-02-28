<?php
//セッションを利用可能にする
session_start();

// セッション情報を削除
$_SESSION = array();
session_destroy();

if($_GET['time_out'] == 1) {
    echo '長時間操作がなかったのでログアウトしました';
} else {
    echo 'ログアウトが完了しました';
}
?>

<a href="login.php">ログインページに戻る</a>