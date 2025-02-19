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
  <title>カテゴリ作成</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/category.css">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
    <div class="content-wrapper">
        <h1>カテゴリ作成</h1>
        <form action="/" method="post" class="category-form">
        <div class="form-group">
            <label for="category_name">カテゴリ名</label>
            <input type="text" id="category_name" name="category_name" required>
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