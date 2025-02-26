<?php
//セッションを利用可能にする
session_start();

// セッション情報を削除
$_SESSION = array();
if(ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"],$params["domain"],
        $params["secure"],$params["httponly"]
    );
} 
unset($_SESSION['User']['id']) ;

//Cookie情報も削除
setcookie('login_name', '', time() - 3600);
setcookie('password', '', time() - 3600);

echo 'ログアウトが完了しました';
?>

<a href="login.php">ログインページに戻る</a>