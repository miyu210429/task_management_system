<!-- default.php -->
<aside class="sidebar">
  <div class="user-info">
    <p class="username">
      <?php echo get_greeting()."<br>"?>
      <?php echo h($login_user['nickname']);?>さん
    </p>
  </div>
  <nav class="menu">
    <ul>
      <li><a href="/task_list.php">タスク一覧</a></li>
      <li><a href="/task_create.php">新規タスク作成</a></li>
      <li><a href="/account_list.php">アカウント一覧</a></li>
      <li><a href="/category_list.php">カテゴリ一覧</a></li>
      <li><a href="/logout.php">ログアウト</a></li>
    </ul>
  </nav>
</aside>