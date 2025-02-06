<?php
//セッションを利用可能にする
session_start();

//セッションの情報を空にする
$_SESSION['User']['id'] = '';

echo 'ログアウトが完了しました';