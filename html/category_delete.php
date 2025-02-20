
<?php
require_once '../app/config.php';
require_once '../app/autoload.php';
require_once '../app/auth.php';

// ログインしているユーザーが特権ユーザーであるかチェック
if ($login_user['is_privileged'] !== 1) {
    header("Location: /category_list.php");
    exit();
}

$category = new Category();

//$_REQUEST['id']のカテゴリが存在しているかチェック
$delete_target = $category->getByCategoryId($_REQUEST['id']);
if (!$delete_target) {
    header('Location: category_list.php');echo  exit();
}

//削除する
$category->deleteCategory($_REQUEST['id']);

//すでに削除されていないかチェック
if ($delete_target['is_deleted'] === 1) {
    echo 'そのカテゴリは削除されています<br />';
}   
echo '削除が完了しました';

?>

<a href="category_list.php">戻る</a>