<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

$user = new User();
$task = new Task();
$category = new Category();

$update_task = $task->getByTaskId($_REQUEST['task_id']);

if(!$update_task) {
    header("Location: /task_list.php") ;exit();
}
//すべてのユーザーのidとnicknameの情報を取ってくる
$fields = 'id,nickname';
$all_users = $user->getAllUsers($fields);

//すべてのカテゴリの情報を取ってくる
$all_categories = $category->getAllCategories();

//親タスクのidから子タスクの情報を取得する
$child_tasks = $task->getChildTasks($update_task['id']);

//編集するタスクの情報を取ってくる
$progresses = $task->getProgressLabels();

if(!empty($_POST)) {
    $error_conditions = $task->validateUpdateInput($_POST);
    
    if(empty($error_conditions)){

        $update_array['category_id'] = $_POST['category_id'];
        $update_array['name'] = $_POST['name'];
        $update_array['detail'] = $_POST['detail'];
        $update_array['user_id'] = $_POST['user_id'];
        $update_array['progress'] = $_POST['progress'];
        $update_array['deadline'] = $_POST['deadline'];



        $primary_key = (int) $_REQUEST['task_id'];
        $task->update($primary_key,$update_array);
        header("Location: /task_list.php") ;exit();
    } else {
        $update_task['category_id'] =  $_POST['category_id'];
        $update_task['name'] = $_POST['name'];
        $update_task['detail'] = $_POST['detail'];
        $update_task['progress'] = $_POST['progress'];
        $update_task['user_id'] = $_POST['user_id'];
    }   
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タスク更新</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/task.css">
  <link rel="icon" href="./img/favicon.svg">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <h1>タスクID:<?php echo $update_task['id'] ?>の更新</h1>
            <form action="" method="post" class="task-form">

            <div class="form-group">
                <label for="category_id">カテゴリ</label>
                <select id="category_id" name="category_id">
                <option value="">未設定</option>
                <?php  foreach ($all_categories as $key => $category_info) :?>
                    <option value="<?php echo $key;?>"
                     <?if(isset($update_task['category_id']) && $update_task['category_id'] == $key):?>selected<?php endif;?>>
                        <?php echo $category_info['category_name'];?>
                    </option>
                <?php    endforeach ?>
                </select>
                <?php 
                    if (isset($error_conditions['category_id']) && is_string($error_conditions['category_id'])) echo $error_conditions['category_id'];
                ?>
            </div>

            <div class="form-group">
                <label for="name">タスク名</label>
                <input type="text" id="name" name="name" value="<?php echo h($update_task['name']); ?>">
            <?php
            if(isset($error_conditions['name']) && is_string($error_conditions['name'])){ echo $error_conditions['name']; }?>
            </div>
            
            <div class="form-group">
                <label for="detail">タスク詳細</label>
                <textarea id="detail" name="detail" rows="15"><?php echo h($update_task['detail']); ?>
                </textarea>
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
                    <?php 
                    if(isset($update_task['user_id']) && $update_task['user_id'] == $user_info['id']):?>selected<?php endif;?>>
                        <?php echo $user_info['nickname'];?>
                    </option>
                <?php endforeach ?>
                </select>
            <?php 
                if (isset($error_conditions['user_id']) && is_string($error_conditions['user_id'])) echo $error_conditions['user_id'];
            ?>
            </div>

            <div class="form-group">
                <label for="progress">進捗</label>
                <select id="progress" name="progress">
                <option value="">-- 選択してください --</option>
                <?php foreach($progresses as $key =>  $progress):?>
                    <option value="<?php echo $key;?>"<? if(isset($update_task['progress']) && $update_task['progress'] == $key):?>selected<?php endif;?>>
                        <?php echo $progress;?>
                    </option>
                <? endforeach;?>
                </select>
                <?php 
                if (isset($error_conditions['progress']) && is_string($error_conditions['progress'])) echo $error_conditions['progress'];
                ?>

            </div>
            
            <div class="form-group">
                <label for="deadline">タスク期限</label>
                <input type="date" id="deadline" name="deadline" value="<?php  echo h($update_task['deadline']);?>">
                <?php 
                if(isset($error_conditions['deadline']) && is_string($error_conditions['deadline'])) echo $error_conditions['deadline'];
                 ?>
            </div>
            
            <div class="form-group">
                <button type="submit">更新</button>
            </div>
            </form>
        </div>
        
        <?php if(!isset($update_task['parent_task_id'])) :?>
        <div class="child-task-create">
            <a href="task_create.php?parent_task_id=<?php echo h($update_task['id']) ?>" class="btn-create">
                <?php echo h($update_task['name']); ?>の子タスク作成</a> 
        </div>
        <?php endif; ?>

        <?php if($child_tasks): ?>
        <section class="task-list-section">
            <!-- ヘッダー行 -->
            <div class="task-list-header">
                <div class="task-cell cell-id">ID</div>
                <div class="task-cell cell-category">カテゴリ</div>
                <div class="task-cell cell-title">タスク名</div>
                <div class="task-cell cell-assignee">担当者</div>
                <div class="task-cell cell-status">進捗状況</div>
                <div class="task-cell cell-deadline">タスク期限</div>
                <div class="task-cell cell-edit">編集</div>
                <div class="task-cell cell-delete">削除</div>
            </div>
            <?php foreach($child_tasks as $child_task): ?>
            <div class="task-row">
                <div class="task-cell cell-id"><?php echo h($child_task['id']) ?></div>
                <div class="task-cell cell-category">
                    <?php echo $child_task['category_name'] == NULL ? '未設定': h($child_task['category_name']) ?>
                </div>

                <div class="task-cell cell-title"><?php echo h($child_task['name']) ?></div>
                <div class="task-cell cell-assignee"><?php  echo h($child_task['nickname']); ?></div>
                
                <div class="task-cell cell-status">
                    <?php echo h(Task::getProgressLabels($child_task['progress'])); ?>
                </div>
                
                <div>
                    <?php 
                        $child_task['deadline'] = dateCalligraphy($child_task['deadline'],false);
                        
                        if($child_task['deadline'] < date("Y-m-d")){?>
                        <font color="red"><?php echo h($child_task['deadline']) ?></font>
                    <?php } else { ?> 
                    <p> <?php echo h($child_task['deadline']); }?> </p>
                </div>
        
                
                <div class="task-cell cell-edit">
                    <a href="task_update.php?task_id=<?php echo($child_task['id'])?>" target="_blank">編集</a>
                </div>
                <div class="task-cell cell-delete">
                    <a href="task_delete.php?task_id=<?php echo h($child_task['id'])?>" onclick="return confirm('本当に削除しますか？');" target="_blank">削除</a>
                </div>
            </div>
            <?php endforeach ?>
        </section>
        <?php endif; ?>
    </main>




  </div>
</body>
</html>