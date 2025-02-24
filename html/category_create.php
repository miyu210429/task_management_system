<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

// ログインしているユーザーが特権ユーザーであるかチェック
if ($login_user['is_privileged'] !== 1) {
  header("Location: /category_list.php");
  exit();
}

$category = new Category();

if(!empty($_POST)) {
  $error_conditions = $category->validateInsertInput($_POST);

  // エラーが無ければデータベースにINSERTする
  if(empty($error_conditions)) {
    $insert_array['category_name'] = $_POST['category_name'];
    $insert_array['last_update_user_id'] = $login_user['id'];

    $category->insert($insert_array);

    header("Location: /category_list.php");exit();
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カテゴリ作成</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/category.css">
  <link rel="icon" href="./img/favicon.svg">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
    <div class="content-wrapper">
        <h1>カテゴリ作成</h1>
        <form action="" method="post" class="category-form">
        <div class="form-group">
            <label for="category_name">カテゴリ名</label>
            <input type="text" id="category_name" name="category_name" value="<?php if(isset($_POST['category_name']))
echo h($_POST['category_name']); ?>">
                <?php 
                if(isset($error_conditions['category_name']) && is_string($error_conditions['category_name'])) echo $error_conditions['category_name'];
                ?>
        </div>
        <div class="form-group">
            <button type="submit">作成</button>
        </div>
        </form>
    </div>
</main>



  </div>
</body>
</html>