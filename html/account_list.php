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
                <a href="/account_create.php" class="btn-create">新規アカウント作成</a>
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

            <?php for($i=0;$i<30;$i++){?>
            <div class="account-row">
                <div class="account-cell cell-id"><?php echo $i+1;?></div>
                <div class="account-cell cell-username">super miyumiyu</div>
                <div class="account-cell cell-registration">2025-02-01</div>
                <div class="account-cell cell-update">2025-02-02</div>
                <div class="account-cell cell-edit">
                <a href="/account_update.php">編集</a>
                </div>
                <div class="account-cell cell-delete">
                <a href="/" onclick="return confirm('本当に削除しますか？');">削除</a>
                </div>
            </div>
            <?php }?>
            </div>
        </div>

    </main>


  </div>
</body>
</html>