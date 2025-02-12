<?php
require_once '../app/config.php';
require_once '../app/autoload.php';
require_once '../app/auth.php';

$task = new Task();

//リクエストのtask_idが存在しているタスクかどうかチェック
$delete_target = $task->getByTaskId($_REQUEST['task_id']);
if (!$delete_target) {
    header('Location: task_list.php');echo  exit();
}

if (!isset($_REQUEST['task_id']) || $login_user['is_privileged'] !== 1){
    header('Location: task_list.php');echo  exit();
}

//特に異常がなければ削除
$delete_task = $task->deleteTask($_REQUEST['task_id']);

//削除が正常にできていれば完了したことを表示
if($delete_task) {
    echo '削除が完了しました<br />';
}
?>
<a href="task_list.php">タスク一覧へ戻る</a>


