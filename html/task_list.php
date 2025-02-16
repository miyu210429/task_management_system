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


//まずタスクの数だけを取得する（SQLでcount(*)とかでとってくる）

if(isset($_GET['s'])){ //getクエリにs(検索フォームのhidden要素)があったら検索している判定
    $tasks = $task->searchCount($_GET);
} else { //検索でなければ全県取得
    $tasks = $task->getAllTaskCount();
}



$progresses = $task->getPregressLabels();

//ページャー機能
if (isset($_REQUEST['page'])) {
    $page = (int) $_REQUEST['page'];
} else {
    $page = 1;
}

if(isset($tasks)){
    $page = max($page, 1);
    $maxPage = ceil($tasks['task_count'] / 5);
    $page = min($page, $maxPage);

    if (isset($_REQUEST['page']) && $_REQUEST['page'] < $page || isset($_REQUEST['page']) && $_REQUEST['page'] > $maxPage || !isset($_REQUEST['page'])) {
        $page = 1;
    }
    
    $start = ($page - 1) * 5;
    //検索ありなしの最大ページ数がわかったら改めてタスクを取得する
    
    if(isset($_GET['s'])) {
        $tasks = $task->search($_GET, $start);
    } else {
        $tasks = $task->getTaskPage($start);
    }
    
}







  //もう１度$maxPageを作って、一回で五件までしか表示できないようにしたい
  //検索したときにpage=をつくりたい
 


    


 

//タスクのぺージャーすること（member.phpを参考にする）
/*
・・投稿を取得するせいぎょ
・・$_REQUEST['page']があれば＄pageにそれをいれ、なければ１
・・$_REQUEST['page']と検索のゲットクエリが重なりそうなんだけど、、
//検索のurlを取得して同じように表示させたい
//ｓ＝１のあとにpage=1とかをひょうじさせたい（<form action="" method="get" class="search-form">みたいに$_GETでやれるかな）
//ページをまたいでも検索機能を引き継ぎたい


//ページャーの機能
foreachでやりたい
現在のページ$page
$maxPageをもととした配列を作る？　$maxPageが３なら３になるまでforで回して配列を作成する
[0] => 1
[1] => 2
[2] => 3

//のりちゃんのアドバイスめも
とりあえず手続き型、そのあとオブジェクトではなく関数でページのかずをかえしてくれるものをつくる（function.phpにおく）
ページは５件まで表示する

$maxPageが１０だとする(ページが５以上ある例)
$maxPageをnとして、カレントをpとする
ｐが１２３までなら表示は１２３４５でいい　カレントの位置は左、左から二番め
ｐが４なら２３４５６
８まではそんなかんじで変化していく
ｐが８９１０なら表示は変えずにカレントの位置のみ変わっていく

//foreachならvalueが１以上$maxPage以下であることを確認しなければならない

//２３４５６でカレントが４ならforのすたーとは２にすればよい


//345678のときはうまくいく例
$current_page = 9;
$maxmax_page = 10;
*/
//$maxmax_pageが５より小さかったらの場合も作らないといけない
function hoge(int $current_page,int $maxmax_page) :array {
    
    if($current_page === 1) {
        $return =[$current_page,$current_page+1,$current_page+2,$current_page+3,$current_page+4]; //1
    } elseif($current_page === 2) {
        $return =[$current_page-1,$current_page,$current_page+1,$current_page+2,$current_page+3]; //2
    } elseif($current_page == $maxmax_page-1) {
        $return =[$current_page-3,$current_page-2,$current_page-1,$current_page,$current_page+1]; //9
    } elseif($current_page == $maxmax_page) {
        $return =[$current_page-4,$current_page-3,$current_page-2,$current_page-1,$current_page]; //10
    } else {
        $return =[$current_page-2,$current_page-1,$current_page,$current_page+1,$current_page+2]; //345678
    }
    return $return;
}

$current_page = $page;
$maxmax_page = $maxPage;
$page_array = hoge($current_page,$maxmax_page);


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
                <option value="">-- 全て --</option>
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
            <?php foreach($tasks as $task){ ?>
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
                <a href="task_update.php?task_id=<?php echo($task['id'])?>">編集</a>
                </div>
                <div class="task-cell cell-delete">
                <?php if ($login_user['is_privileged'] === 1 && $_SESSION['User']['id'] !== $task['user_id']): ?>
                <a href="task_delete.php?task_id=<?php echo h($task['id'])?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                <?php endif ?>
                </div>
            </div>
            <?php  } ?>
            </section>
            
            <?php /*
            現在のページ数を取得したり最大ページ数を取得して
            うまい具合にcurrentと表示するページ数を調整してくださいな
            */
            $url = removeCurrentPage($_SERVER['REQUEST_URI']);
            ?>
            <nav class="pagination">
            <ul><?php if(isset($_GET['s'])){$page_url = '&page=';} else {$page_url = '?page=';}?>
            <li><a href="<?php echo h($url.$page_url); echo 1 ?>" class="prev">最初へ</a></li>
            <?php
                foreach($page_array as $pager => $page_number) : 
                if($current_page === $page_number){?>
                    <li><span class="current"><?php echo h($page_number) ?></span></li>
            <?php } else {?>
        
                    <li><a href="<?php echo h($url.$page_url.$page_number)?>"><?php echo h($page_number) ?></a></li>
            <?php } endforeach ?>
                <li><a href="<?php echo h($url.$page_url.$maxPage) ?>" class="next">最後へ</a></li>
            </ul>
            </nav>
        </div>
    </main>




  </div>
</body>
</html>