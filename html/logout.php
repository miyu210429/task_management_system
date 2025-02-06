<?php
//セッションを利用可能にする
session_start();

//セッションの情報を空にする
unset($_SESSION['User']['id']) ;

echo 'ログアウトが完了しました';
?>

<a href="login.php">ログインページに戻る</a>