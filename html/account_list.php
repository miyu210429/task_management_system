<?php
session_start();

require_once '../app/config.php';
require_once '../app/autoload.php';
require_once '../app/functions.php';

//ログインしているかチェック
if (empty($_SESSION['User']['id'])) {
  header('Location: login.php');
}

$account = new User() ;
$login_user = $account->getById($_SESSION['User']['id']);

//ログインIDからユーザーの情報を取得
$users = $account->getAllUsers();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アカウント一覧</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/account_list.css">
</head>
<body>
  <div class="container">
  <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="header">
                <h1>アカウント一覧</h1>
                <?php if ($login_user['is_privileged'] === 1): ?><a href="/account_create.php" class="btn-create">新規アカウント作成</a> <?php endif ?>
            </div>

            <!-- アカウント一覧のテーブル -->
            <div class="account-list">
            <!-- ヘッダー行 -->
            <div class="account-row header-row">
                <div class="account-cell cell-id">ID</div>
                <div class="account-cell cell-username">ニックネーム</div>
                <div class="account-cell cell-registration">アカウント登録日</div>
                <div class="account-cell cell-update">情報更新日</div>
                <div class="account-cell cell-edit">情報更新</div>
                <div class="account-cell cell-delete">削除</div>
            </div>

            <?php foreach ($users as $user ) :?>
            <div class="account-row">
                <div class="account-cell cell-id"><?php echo $user['id'] ;?></div>
                <div class="account-cell cell-username"><?php echo $user['nickname'] ?></div>
                <div class="account-cell cell-registration"><?php echo $user['created_at'] ?></div>
                <div class="account-cell cell-update"><?php echo $user['update_at'] ?></div>
                <div class="account-cell cell-edit">

                  <?php if ($login_user['is_privileged'] === 1 || $_SESSION['User']['id'] === $user['id']): ?><a href="/account_update.php">編集</a> <?php endif ?>

                </div>
                  <?php if ($login_user['is_privileged'] === 1 && $_SESSION['User']['id'] !== $user['id']): ?>削除</a>
                <div class="account-cell cell-delete">
                <a href="/" onclick="return confirm('本当に削除しますか？');"> <?php endif ?>
                 
                </div>
            </div>
            <?php endforeach ;?>
            </div>
        </div>

    </main>


  </div>
</body>
</html>