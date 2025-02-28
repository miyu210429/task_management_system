<?php
session_start();

require_once __DIR__ . '/autoload.php'; 
require_once __DIR__ . '/classes/User.php';

//ログインしているかチェック
//リクアイアすると、$_SESSIONは常に存在していないから、login.phpにずっと飛ばされる
//login.phpのときは$_SESSION['User']['id']が存在していないかチェックしない
if($_SERVER['REQUEST_URI'] != '/login.php' && !isset($_SESSION['User']['id'])) {
    header("Location: /login.php");
    exit();
}

//ログアウトしていないユーザーが来た場合はタスク一覧ページにとばす

if(isset($_SESSION['User']['id']) && $_SERVER['REQUEST_URI'] != '/task_list.php'){
    header('Location: task_list.php'); exit();
}


if(isset($_SESSION['User']['id'])){

    // ログイン中のユーザー情報を取得
    $user = new User();
    $login_user = $user->getById($_SESSION['User']['id']);
    $user = null; //本来はこういう書き方しないけど後々$userでインスタンス作ってるのでこのインスタンスはここで破棄

    //ユーザーが見つからない場合はログアウト処理
    if (!$login_user) {
        session_destroy();
        header("Location: /login.php");
        exit();
    }
        
}
