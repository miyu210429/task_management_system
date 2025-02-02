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
            <form action="/" method="post" class="task-form">
            <div class="form-group">
                <label for="task_name">タスク名</label>
                <input type="text" id="task_name" name="task_name" required>
            </div>
            
            <div class="form-group">
                <label for="task_detail">タスク詳細</label>
                <textarea id="task_detail" name="task_detail" rows="15" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="assignee">担当者</label>
                <select id="assignee" name="assignee" required>
                <option value="">-- 選択してください --</option>
                <option value="1">美優</option>
                <option value="2">明典</option>
                <option value="3">麻紗美</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deadline">タスク期限</label>
                <input type="date" id="deadline" name="deadline" required>
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