'use strict';

// ボタンの要素を取得
const btn1 = document.getElementById('sl1');
const btn2 = document.getElementById('sl2');
const btn3 = document.getElementById('sl3');
const btn4 = document.getElementById('sl4');
const btn5 = document.getElementById('sl5');
// 戻るボタン
const prevBtn = document.getElementById('prev');
// 進むボタン
const nextBtn = document.getElementById('next');

// 現在のスライドのインデックスを管理
let currentSlide = 0;
const slides = ['top/image/top-slider1.jpg', 'top/image/top-slider2.jpg', 'top/image/top-slider3.jpg', 'top/image/top-slider4.jpg', 'top/image/top-slider5.jpg'];

// 関数: スライダー画像を切り替える
function changeSlide(index) {
  const slider1 = document.getElementById('slider1');
  const slider2 = document.getElementById('slider2');
  const slider3 = document.getElementById('slider3');

  slider1.src = slides[index % slides.length];
  slider2.src = slides[(index + 1) % slides.length];
  slider3.src = slides[(index + 2) % slides.length];

  // ボタンの背景色をリセット
  [btn1, btn2, btn3, btn4, btn5].forEach(btn => btn.style.backgroundColor = 'rgba(0,0,0,0.5)');

  // 現在表示されている3つのスライドに対応するボタンの背景色を変更
  [btn1, btn2, btn3, btn4, btn5][index % slides.length].style.backgroundColor = 'rgba(0,0,0,1)';
  [btn1, btn2, btn3, btn4, btn5][(index + 1) % slides.length].style.backgroundColor = 'rgba(0,0,0,1)';
  [btn1, btn2, btn3, btn4, btn5][(index + 2) % slides.length].style.backgroundColor = 'rgba(0,0,0,1)';
}

// 関数: 画面幅に応じてスライダーの動作を変更
function run() {
  if (window.innerWidth < 768) {
    // スクロールボタンを非表示にする（スマホ向け）
    prevBtn.style.display = 'none';
    nextBtn.style.display = 'none';

    // スマホ向けのクリックイベント
    [btn1, btn2, btn3, btn4, btn5].forEach((btn, index) => {
      btn.onclick = function () {
        document.getElementById('slider1').src = slides[index];
        [btn1, btn2, btn3, btn4, btn5].forEach(b => b.style.backgroundColor = 'rgba(0,0,0,0.5)');
        btn.style.backgroundColor = 'rgba(0,0,0,1)';
      };
    });
  } else {
    // PC向けの動作
    // スクロールボタンを表示
    prevBtn.style.display = 'block';
    nextBtn.style.display = 'block';

    // 初期スライドの表示
    changeSlide(currentSlide);

    // スクロール戻るボタンの動作
    prevBtn.onclick = function () {
      currentSlide = (currentSlide - 1 + slides.length) % slides.length;
      changeSlide(currentSlide);
    };

    // スクロール進むボタンの動作
    nextBtn.onclick = function () {
      currentSlide = (currentSlide + 1) % slides.length;
      changeSlide(currentSlide);
    };

    // ボタンのクリックイベント
    [btn1, btn2, btn3, btn4, btn5].forEach((btn, index) => {
      btn.onclick = function () {
        currentSlide = index;
        changeSlide(currentSlide);
      };
    });
  }
}

// 初回読み込み時と画面サイズ変更時に関数を実行
run();
window.addEventListener('resize', run);
