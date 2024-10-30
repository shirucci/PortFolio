<?php
// セッションの開始
session_start();

// 現在の年と月を取得
$year = date('Y');
$month = date('m');

// GETリクエストで月を変更
if (isset($_GET['month']) && isset($_GET['year'])) {
  $month = (int)$_GET['month'];
  $year = (int)$_GET['year'];

  // 月が1より小さい場合は、前年の12月に戻す
  if ($month < 1) {
    $month = 12;
    $year--;
  }

  // 月が12より大きい場合は、翌年の1月に進める
  if ($month > 12) {
    $month = 1;
    $year++;
  }
}

// 月の日数と最初の曜日を再計算
$daysInMonth = date('t', strtotime("$year-$month-01"));
$firstDayOfMonth = date('w', strtotime("$year-$month-01"));

// データベース接続
try {
  $pdo = new PDO('mysql:host=mysql1.php.starfree.ne.jp; dbname=ca18detyasu_calendar; charset=utf8', 'ca18detyasu_cal', 'password');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Database connection failed: ' . $e->getMessage();
  exit;
}

// データベースからタイトルとtimeフィールドを取得
$stmt = $pdo->prepare('SELECT title, date, time FROM events WHERE YEAR(date) = :year AND MONTH(date) = :month');
$stmt->execute(['year' => $year, 'month' => $month]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// イベントを日付ごとに整理する配列
$eventsByDate = [];
foreach ($events as $event) {

  // 日付を取得
  $day = date('j', strtotime($event['date']));
  if (!isset($eventsByDate[$day])) {
    $eventsByDate[$day] = [];
  }
  // タイトルと時間を保存
  $eventsByDate[$day][] = ['title' => $event['title'], 'time' => $event['time']];
}

?>

<!DOCTYPE html>

<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>challenge-calender-5</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
  </head>

  <body>

    <p class="header">
      <button id="prev-button">&lt;</button>
      <span class="header-title" id="calendar-header">
        <?php echo $year, '年 ', sprintf('%02d', $month), '月'; ?>
      </span>
      <button id="next-button">&gt;</button>
    </p>

   <div class="calendar">
    <?php
    // メッセージを表示
    if (isset($_SESSION['message'])) {
      echo '<p class="message">' . htmlspecialchars($_SESSION['message']) . '</p>';

      // メッセージを表示後に削除
      unset($_SESSION['message']);
    }
    ?>

      <table id="calendar-table">
        <?php
        // 曜日ヘッダーを表示
        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
          echo '<tr>', "\n";
          foreach ($daysOfWeek as $day) {
            echo '<th>', $day, '</th>', "\n";
          }
          echo '</tr>', "\n";

        // カレンダーの日付を表示
        $currentDay = 1;
        $i = 0;
        echo '<tr>', "\n";

        // 月の初めの空欄を生成
        for ($i = 0; $i < $firstDayOfMonth; $i++) {
          echo '<td></td>', "\n";
        }

        // 日付を生成
        while ($currentDay <= $daysInMonth) {
          if ($i % 7 == 0 && $i > 0) {
            // 新しい行を開始
            echo '</tr>', "\n", '<tr>', "\n";
          }

        // 今日の日付を特定してinput.phpに日付パラメータを渡すリンクを作成
        if ($currentDay == date('j') && $month == date('n') && $year == date('Y')) {
          echo '<td><a class="current-day" href="input.php?date=', $year, '-', sprintf('%02d', $month), '-', sprintf('%02d', $currentDay), '">';
        } else {
          echo '<td><a href="input.php?date=', $year, '-', sprintf('%02d', $month), '-', sprintf('%02d', $currentDay), '">';
        }

        echo "<p class='day'>{$currentDay}</p>";

        // イベントがある場合、そのタイトルと時間を表示
        if (isset($eventsByDate[$currentDay])) {
         foreach ($eventsByDate[$currentDay] as $event) {
            echo '<p class="event-title"><span class="event">', htmlspecialchars($event['time']), ' ', htmlspecialchars($event['title']), '</span></p>';
         }
        }

        echo '</a></td>', "\n";

        $currentDay++;
        $i++;
        }

        // 最後の行の残りを埋める
        while ($i % 7 != 0) {
          echo '<td></td>', "\n";
          $i++;
        }

        echo '</tr>', "\n";
        ?>
      </table>
    </div>

    <script>
    'use strict';

    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');
    const calendarHeader = document.getElementById('calendar-header');
    const calendarTable = document.getElementById('calendar-table');

    let currentYear = <?php echo $year; ?>;
    let currentMonth = <?php echo $month; ?>;

    function updateCalendar(year, month) {

    // 月をゼロ埋めして2桁に
    const paddedMonth = String(month).padStart(2, '0');
    fetch(`?year=${year}&month=${paddedMonth}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(html => {

        // カレンダーの内容を更新
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newCalendarTable = doc.getElementById('calendar-table');

        // カレンダーのテーブルを新しい内容で置き換え
        calendarTable.innerHTML = newCalendarTable.innerHTML;

        // ヘッダーも更新
        calendarHeader.textContent = `${year}年 ${paddedMonth}月`;
     })
      .catch(error => {
        console.error('Fetch error:', error);
      });
    }

    // 前の月へ移動
    prevButton.addEventListener('click', (e) => {
      e.preventDefault();
      currentMonth--;
      if (currentMonth < 1) {
       currentMonth = 12;
       currentYear--;
      }
     updateCalendar(currentYear, currentMonth);
    });

    // 次の月へ移動
    nextButton.addEventListener('click', (e) => {
      e.preventDefault();
      currentMonth++;
      if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
      }
      updateCalendar(currentYear, currentMonth);
    });

      // キーボードのキーイベントを追加
      document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') {

          // 右キーが押された場合
          currentMonth++;
          if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
          }
          updateCalendar(currentYear, currentMonth);
        } else if (e.key === 'ArrowLeft') {

          // 左キーが押された場合
          currentMonth--;
          if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
          }
          updateCalendar(currentYear, currentMonth);
        }
      });

      // フリック操作のための変数
      // タッチ開始位置
      let touchStartX = 0;

      // タッチ終了位置
      let touchEndX = 0;

      // タッチ開始時の処理
      document.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
      });

      // タッチ終了時の処理
      document.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleGesture();
      });

      // ジェスチャー処理
      function handleGesture() {
        if (touchEndX < touchStartX - 50) {

          // 左にフリックした場合
          currentMonth++;
          if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
          }
          updateCalendar(currentYear, currentMonth);
        } else if (touchEndX > touchStartX + 50) {

          // 右にフリックした場合
          currentMonth--;
          if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
          }
          updateCalendar(currentYear, currentMonth);
        }
      }
    </script>
  </body>

</html>