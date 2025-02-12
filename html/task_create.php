<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';


$user = new User();
$task = new Task();

//すべてのユーザーのidとnicknameの情報を取ってくる
$files = 'id,nickname';
$all_users = $user->getAllUsers($files);


if (!empty($_POST)) {
    //バリデーションをチェック
    $error_conditions = $task->validateInsertInput($_POST);
    if (empty($error_conditions)) {

        $insert_array['name'] = $_POST['name'];
        $insert_array['detail'] = $_POST['detail'];
        $insert_array['user_id'] = $_POST['user_id'];
        $insert_array['deadline'] = $_POST['deadline'];

        $task->insert($insert_array);

        header("Location: /task_list.php") ;exit();


    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タスク作成</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/task.css">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <h1>タスク作成</h1>
            <form action="" method="post" class="task-form">
            <div class="form-group">
                <label for="name">タスク名</label>
                <input type="text" id="name" name="name" value="<?php if(isset($_POST['name']))
echo h($_POST['name']); ?>">
                <?php 
                if(isset($error_conditions['name']) && is_string($error_conditions['name'])) echo $error_conditions['name'];
                ?>
            </div>
            
            <div class="form-group">
                <label for="detail">タスク詳細</label>
                <textarea id="detail" name="detail" rows="15"><?php  if(isset($_POST['detail'])) echo h($_POST['detail']) ?></textarea>
            <?php 
            if(isset($error_conditions['detail']) && is_string($error_conditions['detail'])) echo $error_conditions['detail'];
            ?>
            </div>
            
            <div class="form-group">
                <label for="user_id">担当者</label>
                <select id="user_id" name="user_id">
                <option value="">-- 選択してください --</option>
                <?php  foreach ($all_users as $user_info) : ?>
                    <option value="<?php echo $user_info['id']?>"
                     <?if(isset($_POST['user_id']) && $_POST['user_id'] == $user_info['id']):?>selected<?php endif;?>>
                        <?php echo $user_info['nickname'];?>
                    </option>
                <?php endforeach ?>
                </select>
            <?php 
                if (isset($error_conditions['user_id']) && is_string($error_conditions['user_id'])) echo $error_conditions['user_id'];
            ?>
            

            </div>
            
            <div class="form-group">
                <label for="deadline">タスク期限</label>
                <input type="date" id="deadline" name="deadline" value="<?php if(isset($_POST['deadline']))
echo h($_POST['deadline']); ?>">
                <?php 
                if(isset($error_conditions['deadline']) && is_string($error_conditions['deadline'])) echo $error_conditions['deadline'];
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