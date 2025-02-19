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

$progresses = $task->getProgressLabels();

//まずタスクの数だけを取得する（SQLでcount(*)とかでとってくる）
if(isset($_GET['s'])){ //getクエリにs(検索フォームのhidden要素)があったら検索している判定
    $tasks = $task->searchCount($_GET);
} else { //検索でなければ完了以外のものを取得（デフォルトの表示）
    $tasks = $task->getUnfinishedTaskCount();
}

//タスクの最大ページ、現在のページ、データベースからSELECTする際に必要なタスクのスタートとなる数字を取得
$request_page = null;
if(isset($_REQUEST['page'])){
    $request_page = $_REQUEST['page'];
}
$page_info = getPageCount($request_page, $tasks['task_count']);


//検索ありなしの最大ページ数がわかったら改めてタスクを取得する
//URLに表示させる文字を指定する
if(isset($_GET['s'])) {
    $tasks = $task->search($_GET, $page_info['start']);
    $page_url = '&page='; 
} else {
    $tasks = $task->getTaskPage($page_info['start']);
    $page_url = '?page='; 
}
  
//ページャーの数字を取得
$page_array = getPaginationRange($page_info['current_page'],$page_info['maxPage']);

//現在のURLから$_GET['page']を削除する
$pager_base_url = removeCurrentPage($_SERVER['REQUEST_URI']);



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
            <form action="" method="get" class="search-form">
                <div class="form-group">
                <label for="user_id">担当者</label>
                <select name="user_id" id="user_id">
                    <option value="">-- すべて --</option>
                    <?php foreach ($managers as $user_info) : ?>
                    <option value="<?php echo $user_info['id']?>"
                    <? if(isset($_GET['user_id']) && $_GET['user_id'] == $user_info['id']): ?>selected <?php endif ?>>
                        <?php echo $user_info['nickname'];?>
                    </option>
                    <?php endforeach ?>
                    <!-- その他の担当者 -->
                </select>
                </div>

                <div class="form-group">
                <label for="progress">進捗</label>
                <select name="progress" id="progress">
                <option value="">完了以外</option>
                <?php foreach($progresses as $key =>  $progress): ?>
                    <option value="<?php echo $key;?>"<? if(isset($_GET['progress']) && $_GET['progress'] == $key):?>selected <?php endif?>> 
                        <?php echo $progress;?>
                    </option> <?php endforeach ?>
                </select>
                </div>

                <div class="form-group date-range">
                <label>タスク期限</label>
                <div class="date-inputs">
                    <input type="date" name="start_date" value="<?php if(isset($_GET['start_date'])){ echo h($_GET['start_date']);}?>">
                    <span class="date-separator">〜</span>
                    <input type="date" name="end_date" value="<?php if(isset($_GET['end_date'])){ echo h($_GET['end_date']);}?>">
                </div>
                </div>

                <div class="form-group">
                 <input type="hidden" name="s" value="1">
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
            <?php foreach($tasks as $task): ?>
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
                    echo h(Task::getProgressLabels($task['progress']));
                    ?>
                </div>
                
                <div>
                <?php if($task['deadline'] < date("Y-m-d")){?>
                <font color="red"><?php echo h($task['deadline']) ?></font>
                <?php } else { ?> 
                <p> <?php echo h($task['deadline']); }?> </p>
                </div>
                
                
                <div class="task-cell cell-edit">
                <a href="task_update.php?task_id=<?php echo($task['id'])?>">編集</a>
                </div>
                <div class="task-cell cell-delete">
                <?php if ($login_user['is_privileged'] === 1 && $_SESSION['User']['id'] !== $task['user_id']): ?>
                <a href="task_delete.php?task_id=<?php echo h($task['id'])?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                <?php endif ?>
                </div>
            </div>
            <?php  endforeach; ?>
            </section>
    
            <nav class="pagination">
            <ul>
            <li><a href="<?php echo h($pager_base_url.$page_url); echo 1 ?>" class="prev">最初へ</a></li>
            <?php
            foreach($page_array as $page_number) : 
            if($page_info['current_page'] === $page_number) { ?>
                <li><span class="current"><?php echo h($page_number) ?></span></li>
            <?php } else {?>
                <li><a href="<?php echo h($pager_base_url.$page_url.$page_number)?>"><?php echo h($page_number) ?></a></li>
            <?php } endforeach; ?>
                <li><a href="<?php echo h($pager_base_url.$page_url.$page_info['maxPage']) ?>" class="next">最後へ</a></li>
            </ul>
            </nav>
        </div>
    </main>




  </div>
</body>
</html>