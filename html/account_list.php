<?php

require_once '../app/config.php';
require_once '../app/autoload.php';
require_once '../app/functions.php';
 //ログインチェック、ログインできていなければログインページに遷移できていれば$login_userにログイン者の情報が入る
require_once '../app/auth.php';

$user = new User() ;

//ログインIDからユーザーの情報を取得
if(isset($_GET['s'])){ //getクエリにs(検索フォームのhidden要素)があったら検索している判定
  $users = $user->search($_GET);
} else { //検索でなければ全県取得
  $users = $user->getAllUsers();
}


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
            <form action="account_list.php" method="get" class="account-search-form">
              <div class="form-group">
                <label for="login_name">ログイン名</label>
                <input type="text" id="login_name" name="login_name" placeholder="ログイン名を入力" 
                value="<?php if(isset($_GET['login_name'])){ echo h($_GET['login_name']);}?>">
              </div>
              
              <div class="form-group">
                <label for="nickname">ニックネーム</label>
                <input type="text" id="nickname" name="nickname" placeholder="ニックネームを入力" 
                value="<?php if(isset($_GET['nickname'])){ echo h($_GET['nickname']);}?>">
              </div>

              <!-- ニックネーム入力グループの直後に追加 -->
              <div class="form-group checkbox-group">
                <label for="no_task_user">
                  <input type="checkbox" id="no_task_user" name="no_task_user" value="1">
                  タスクを持っていな暇なユーザー
                </label>
              </div>
              
              <div class="form-group date-range">
                <label>更新日（期間指定）</label>
                <div class="date-inputs">
                  <input type="date" name="updated_at_from" 
                  value="<?php if(isset($_GET['updated_at_from'])){ echo h($_GET['updated_at_from']);}?>">
                  <span class="date-separator">〜</span>
                  <input type="date" name="updated_at_to" 
                  value="<?php if(isset($_GET['updated_at_to'])){ echo h($_GET['updated_at_to']);}?>">
                </div>
              </div>
              
              <div class="form-group">
                <input type="hidden" name="s" value="1">
                <button type="submit">検索</button>
              </div>
            </form>

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
                <div class="account-cell cell-update"><?php echo $user['updated_at'] ?></div>
                <div class="account-cell cell-edit">

                <?php if ($login_user['is_privileged'] === 1 || $_SESSION['User']['id'] === $user['id']): ?>
                  <a href="account_update.php?id=<?php echo h($user['id']); ?>">編集</a> 
                <?php endif ?>
                </div>
                 
                <div class="account-cell cell-delete"> 
                <?php if ($login_user['is_privileged'] === 1 && $_SESSION['User']['id'] !== $user['id']): ?>
                  <a href="account_delete.php?id=<?php echo h($user['id']); ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                <?php endif ?>
                </div>
            </div>
            <?php endforeach ;?>
            </div>
        </div>

    </main>


  </div>
</body>
</html>