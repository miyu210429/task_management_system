<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カテゴリ一覧</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/category_list.css">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
        <!-- ヘッダー：ページタイトルと右上の「カテゴリ作成」ボタン -->
            <div class="header">
            <h1>カテゴリ一覧</h1>
            <a href="category_create.php" class="btn-create">カテゴリ作成</a>
            </div>
            
            <!-- カテゴリ一覧の疑似テーブル -->
            <div class="category-list">
            <!-- ヘッダー行 -->
            <div class="category-row header-row">
                <div class="category-cell cell-name">カテゴリ名</div>
                <div class="category-cell cell-updater">最終更新者</div>
                <div class="category-cell cell-created">カテゴリ作成日</div>
                <div class="category-cell cell-edit">編集</div>
                <div class="category-cell cell-delete">削除</div>
            </div>
            
            <div class="category-row">
                <div class="category-cell cell-name">サンプルカテゴリ</div>
                <div class="category-cell cell-updater">new miyu</div>
                <div class="category-cell cell-created">2025-02-01</div>
                <div class="category-cell cell-edit">
                <a href="category_update.php?id=1">編集</a>
                </div>
                <div class="category-cell cell-delete">
                <a href="category_delete.php?id=1" onclick="return confirm('本当に削除しますか？');">削除</a>
                </div>
            </div>

        </div>
    </div>
    </main>




  </div>
</body>
</html>