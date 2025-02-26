<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

// ログインしているユーザーが特権ユーザーであるかチェック
if ($login_user['is_privileged'] !== 1) {
  header("Location: /logout.php");
  exit();
}

$category = new Category();

//全てのカテゴリを取得
$categories = $category->getAllCategories();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カテゴリ一覧</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/category_list.css">
  <link rel="icon" href="./img/favicon.svg">
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
            <?php
            foreach($categories as $display_category):
            if($display_category['user_deleted'] == 1) {
              $display_category['nickname'] = '削除済み';
            }
            ?>
            <div class="category-row">
                <div class="category-cell cell-name"><?php echo h($display_category['category_name'])?></div>
                <div class="category-cell cell-updater"><?php echo h($display_category['nickname']) ?></div>
                <div class="category-cell cell-created">
                  <?php $display_category['created_at'] = dateCalligraphy($display_category['created_at']); echo h($display_category['created_at']);?>
                </div>
                <div class="category-cell cell-edit">
                <a href="category_update.php?category_id=<?php echo h($display_category['id'])?>">編集</a>
                </div>
                <div class="category-cell cell-delete">
                <a href="category_delete.php?category_id=<?php echo h($display_category['id'])?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                </div>
            </div>
            <?php endforeach; ?>


        </div>
    </div>
    </main>




  </div>
</body>
</html>