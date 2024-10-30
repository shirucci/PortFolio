'use strict';

// 残り時間を格納する変数（秒数）
let timeRemaining;

// setIntervalの参照を格納するための変数
let countdownFunction;

// カウントアップモードかどうかを示すフラグ
let isCountingUp = false;
// 一時停止状態かどうかを示すフラグ
let isPaused = false;

// タイマーを開始する関数
function startTimer() {

  // すでにカウントダウンが進行中の場合は何もしない
  if (countdownFunction) return;

  // 一時停止していない場合（新しい入力があるとき）
  if (!isPaused) {

    // ユーザーが入力した時間、分、秒の値を取得し、数値に変換。未入力なら0とする。
    const hours = parseInt(document.getElementById("hoursInput").value) || 0;
    const minutes = parseInt(document.getElementById("minutesInput").value) || 0;
    const seconds = parseInt(document.getElementById("secondsInput").value) || 0;

    // 入力値を合計秒数に変換
    timeRemaining = (hours * 3600) + (minutes * 60) + seconds;

    // 無効な入力値や負の値があった場合、警告を表示して終了
    if (isNaN(timeRemaining) || timeRemaining < 0) {
      alert("正しい数値を入力してください。");
      return;
    }

    // 合計が0秒の場合、カウントアップを開始するフラグをセット
    if (timeRemaining === 0) {
      isCountingUp = true;
    } else {
      isCountingUp = false;
    }
  }

  // カウントアップかカウントダウンかで実行する関数を決め、1秒ごとに実行
  countdownFunction = setInterval(isCountingUp ? countUp : countDown, 1000);

  // 一時停止状態を解除
  isPaused = false;
}

// カウントダウンを処理する関数
function countDown() {
  if (timeRemaining > 0) {

    // 残り時間を1秒減らす
    timeRemaining--;

    // 現在の残り時間を表示
    displayTime(timeRemaining);
  } else {

    // 残り時間が0になったらタイマーを停止
    clearInterval(countdownFunction);
    countdownFunction = null;

    // カウントダウンが終わったら画面全体を点滅させる
    document.body.style.animation = "blink 1s infinite";
    document.querySelector(".btnStart").style.animation = "blink 1s infinite";
    document.querySelector(".btnStop").style.animation = "blink 1s infinite";
    document.querySelector(".btnReset").style.animation = "blink 1s infinite";
  }
}

// カウントアップを処理する関数
function countUp() {

  // 残り時間を1秒増やす（カウントアップなので増加）
  timeRemaining++;

  // 現在の時間を表示
  displayTime(timeRemaining);
}

// タイマーを一時停止する関数
function stopTimer() {

  // カウントダウン・アップを停止し、タイマーをリセット
  clearInterval(countdownFunction);

  // タイマーの参照を削除
  countdownFunction = null;

  // 一時停止状態を記録
  isPaused = true;
}

// タイマーをリセットする関数
function resetTimer() {

  // カウントダウン・アップを完全に停止し、リセットする
  clearInterval(countdownFunction);

  // タイマーの参照を削除
  countdownFunction = null;

  // 点滅アニメーションを解除
  document.body.style.animation = "none";
  document.querySelector(".btnStart").style.animation = "none";
  document.querySelector(".btnStop").style.animation = "none";
  document.querySelector(".btnReset").style.animation = "none";

  // タイマーの表示を00:00:00にリセット
  document.getElementById("timerDisplay").innerHTML = "00&emsp;:&emsp;00&emsp;:&emsp;00";

  // 入力フィールドをクリア
  document.getElementById("hoursInput").value = "";
  document.getElementById("minutesInput").value = "";
  document.getElementById("secondsInput").value = "";

  // リセット後は一時停止状態を解除
  isPaused = false;

  // 残り時間もリセット
  timeRemaining = 0;
}

// 残り時間を表示する関数
function displayTime(time) {

  // 残り時間を時、分、秒に分割し、2桁表示にする
  const hours = String(Math.floor(time / 3600)).padStart(2, '0');
  const minutes = String(Math.floor((time % 3600) / 60)).padStart(2, '0');
  const seconds = String(time % 60).padStart(2, '0');

  // フォーマットしてHTMLに出力
  document.getElementById("timerDisplay").innerHTML = `${hours}&emsp;:&emsp;${minutes}&emsp;:&emsp;${seconds}`;
}

// 入力チェック関数を追加
function checkForInvalidInput(event) {
  const input = event.target.value;

  // 数字以外の文字（全角数字や他の文字列）をチェック
  if (/[^0-9]/.test(input) || /[\uFF10-\uFF19]/.test(input)) {
    alert("半角の数字のみ入力してください！");

    // 半角以外の文字や全角数字を削除
    event.target.value = input.replace(/[^0-9]/g, '');   // 半角数字以外を削除
  }
}

// 入力フィールドにイベントリスナーを追加
document.getElementById("hoursInput").addEventListener("input", checkForInvalidInput);
document.getElementById("minutesInput").addEventListener("input", checkForInvalidInput);
document.getElementById("secondsInput").addEventListener("input", checkForInvalidInput);
