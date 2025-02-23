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

$category_id = (int) $_REQUEST['category_id'];
// 編集するカテゴリの情報を取ってくる
$update_category = $category->getByCategoryId($category_id);

if(!empty($_POST)) {
  $error_conditions = $category->validateUpdateInput($_POST);

  if(empty($error_conditions)) {
    $update_array['category_name'] = $_POST['category_name'];
    $update_array['last_update_user_id'] = $login_user['id'];

    $category->update($update_array,$category_id);
    header("Location: /category_list.php") ;exit();

  } else {
    $update_category['category_name'] = $_POST['category_name'];
  }


}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カテゴリ編集</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/category.css">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
    <div class="content-wrapper">
        <h1>カテゴリ「<?php echo $update_category['category_name']?>」の編集</h1>
        <form action="" method="post" class="category-form">
        <div class="form-group">
            <label for="category_name">カテゴリ名</label>
            <input type="text" id="category_name" name="category_name" value="<?php echo h($update_category['category_name']); ?>">
            <?php
            if(isset($error_conditions['category_name']) && is_string($error_conditions['category_name'])){ echo $error_conditions['category_name']; }?>
        </div>
        <div class="form-group">
            <button type="submit">編集</button>
        </div>
        </form>
    </div>
</main>



  </div>
</body>
</html>