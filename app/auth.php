<?php
session_start();

require_once __DIR__ . '/autoload.php'; 
require_once __DIR__ . '/classes/User.php';

//ログインしているかチェック
//login.phpのときは$_SESSION['User']['id']が存在していないかチェックしない
if($_SERVER['REQUEST_URI'] != '/login.php' && !isset($_SESSION['User']['id'])) {
    header("Location: /login.php");
    exit();
}

//ログアウトしていないユーザーがログインページに来た場合はタスク一覧ページにとばす
if($_SERVER['REQUEST_URI'] == '/login.php' && isset($_SESSION['User']['id'])){
    header('Location: task_list.php'); exit();
}


if(isset($_SESSION['User']['id'])){

    // ログイン中のユーザー情報を取得
    $user = new User();
    $login_user = $user->getById($_SESSION['User']['id']);
    $user = null; //本来はこういう書き方しないけど後々$userでインスタンス作ってるのでこのインスタンスはここで破棄

    //ユーザーが見つからない場合はログインページへ
    if (!$login_user) {
        session_destroy();
        header("Location: /login.php");
        exit();
    }
        
}
