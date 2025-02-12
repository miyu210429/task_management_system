<?php
require_once '../app/autoload.php';
require_once '../app/config.php';
require_once '../app/functions.php';
require_once '../app/auth.php';

$user = new User();
$task = new Task();

//idとnicknameの情報を全ユーザーから取得
$fields = 'id,nickname';
$managers = $user->getAllUsers($fields);

foreach ($managers as $manager) {
    $task_mana[$manager['id']] = $manager['nickname'];
        
}

//すべてのタスクを取得
$tasks = $task->getAllTasks();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タスク一覧</title>
  <link rel="stylesheet" href="/css/common.css">
  <link rel="stylesheet" href="/css/task_list.css">
</head>
<body>
  <div class="container">
    <?php include '../templates/default.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <!-- 検索フォームセクション -->
            <section class="search-form-section">
            <h1>タスク一覧</h1>
            <form action="/" method="get" class="search-form">
                <div class="form-group">
                <label for="assignee">担当者</label>
                <select name="assignee" id="assignee">
                    <option value="">-- すべて --</option>
                    <option value="1">美優</option>
                    <option value="2">明典</option>
                    <option value="3">麻紗美</option>
                    <!-- その他の担当者 -->
                </select>
                </div>

                <div class="form-group">
                <label for="status">ステータス</label>
                <select name="status" id="status">
                    <option value="">-- 全て --</option>
                    <option value="1">未着手</option>
                    <option value="2">進行中</option>
                    <option value="3">確認中</option>
                    <option value="4">完了</option>
                </select>
                </div>

                <div class="form-group date-range">
                <label>タスク期限</label>
                <div class="date-inputs">
                    <input type="date" name="start_date">
                    <span class="date-separator">〜</span>
                    <input type="date" name="end_date">
                </div>
                </div>

                <div class="form-group">
                <button type="submit">検索</button>
                </div>
            </form>
            </section>

            <!-- タスク一覧セクション -->
            <section class="task-list-section">
            <!-- ヘッダー行 -->
            <div class="task-list-header">
                <div class="task-cell cell-id">ID</div>
                <div class="task-cell cell-title">タスク名</div>
                <div class="task-cell cell-assignee">担当者</div>
                <div class="task-cell cell-status">進捗状況</div>
                <div class="task-cell cell-deadline">タスク期限</div>
                <div class="task-cell cell-edit">編集</div>
                <div class="task-cell cell-delete">削除</div>
            </div>
            <?php foreach($tasks as $task){?>
            <div class="task-row">
                <div class="task-cell cell-id"><?php echo h($task['id']) ?> </div>
                <div class="task-cell cell-title"><?php echo h($task['name']) ?> </div>
                <div class="task-cell cell-assignee">
                    <?php 
                     echo h($task_mana[$task['user_id']]);
                    ?> 
                </div>
                <div class="task-cell cell-status">
                    <?php
                    echo h(Task::getPregressLabels($task['progress']));
                    ?>
                </div>
                
                <div>
                <?php if($task['deadline'] < date("Y-m-d")){?>
                <font color="red"><?php echo h($task['deadline']) ?></font>
                <?php } else { ?> 
                <p> <?php echo h($task['deadline']); }?> </p>
                </div>
                
                
                <div class="task-cell cell-edit">
                <a href="task_update.php?id=<?php $task['id']?>">編集</a>
                </div>
                <div class="task-cell cell-delete">
                <?php if ($login_user['is_privileged'] === 1 && $_SESSION['User']['id'] !== $task['user_id']): ?>
                <a href="task_delete.php?id=<?php $task['id']?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                <?php endif ?>
                </div>
            </div>
            <?php  } ?>
            </section>
            
            <?php /*
            現在のページ数を取得したり最大ページ数を取得して
            うまい具合にcurrentと表示するページ数を調整してくださいな
            */?>
            <nav class="pagination">
            <ul>
                <li><a href="?page=1" class="prev">前へ</a></li>
                <li><a href="?page=1">1</a></li>
                <li><a href="?page=2">2</a></li>
                <li><span class="current">3</span></li>
                <li><a href="?page=4">4</a></li>
                <li><a href="?page=5">5</a></li>
                <li><a href="?page=4" class="next">次へ</a></li>
            </ul>
            </nav>
        </div>
    </main>




  </div>
</body>
</html>