<?php

// セッションの開始
session_start();

// データベース接続
try {
  $pdo = new PDO('mysql:host=mysql1.php.starfree.ne.jp; dbname=ca18detyasu_calendar; charset=utf8', 'ca18detyasu_cal', 'password');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo '<p>データベース接続に失敗しました</p>: ' . $e->getMessage();
  exit;
}

$date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : '';

$event = null;
if ($date) {
  $sql = $pdo->prepare('select * from events where date = :date');
  $sql->execute(['date' => $date]);
  $event = $sql->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'delete') {

    // 削除処理
    $sql = $pdo->prepare('delete from events where date = :date');
    $sql->execute(['date' => $date]);
    $_SESSION['message'] = 'スケジュールを削除しました。';
    header('Location: index.php');
    exit;
  } elseif ($action === 'save') {

  // 保存処理
    $title = $_POST['title'];
    $time = $_POST['time'];
    $category = $_POST['category'];
    $content = $_POST['content'];

    // 更新または新規登録の処理
    if ($event) {
      $sql = $pdo->prepare('update events set title = :title, time = :time, category = :category, content = :content where date = :date');
      $sql->execute(['title' => $title, 'time' => $time, 'category' => $category, 'content' => $content, 'date' => $date]);
      $_SESSION['message'] = 'スケジュールを更新しました。';
    } else {
      $sql = $pdo->prepare('insert into events (title, date, time, category, content) values (?, ?, ?, ?, ?)');
      $sql->execute([$title, $date, $time, $category, $content]);
      $_SESSION['message'] = 'スケジュールを登録しました。';
    }
    header('Location: index.php');
    exit;
  }
}
?>

<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
    <title>challenge-calender-5-input</title>
  </head>

  <body>
    <form action="input.php?date=<?php echo htmlspecialchars($date); ?>" method="post">
      <p class="title">タイトル</p>
      <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" required>

      <p class="title">日付</p>
      <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>

      <p class="title">時間</p>
      <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($event['time'] ?? ''); ?>" required>

      <p class="title">カテゴリー</p>
      <select name="category" class="category" required>
        <option value="" disabled <?php echo !isset($event['category']) ? 'selected' : ''; ?>>選択してください</option>
        <option value="就職活動" <?php echo isset($event['category']) && $event['category'] == '就職活動' ? 'selected' : ''; ?>>就職活動</option>
        <option value="訓練" <?php echo isset($event['category']) && $event['category'] == '訓練' ? 'selected' : ''; ?>>訓練</option>
        <option value="プライベート" <?php echo isset($event['category']) && $event['category'] == 'プライベート' ? 'selected' : ''; ?>>プライベート</option>
      </select>

    <p class="title">内容</p>
    <textarea name="content" class="content" required><?php echo htmlspecialchars($event['content'] ?? ''); ?></textarea>

    <p class="btn">

    <!-- 既存データがある場合に削除ボタンを表示 -->
    <?php
    if ($event):
    ?>

    <button type="submit" class="delete-btn" name="action" value="delete">削除</button>
    <?php
    endif;
    ?>
    <button type="submit" class="submit-btn" name="action" value="save">スケジュールを保存</button>
    </p>
    </form>
  </body>

</html>