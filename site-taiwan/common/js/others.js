'use strict';
// ハンバーガーメニュー　ココカラ
const openbtn = document.querySelector('.openBtn');
const closebtn = document.querySelector('.closeBtn');
openbtn.onclick = function () {
  this.classList.toggle('active');
  document.querySelector('.slideMenu').classList.toggle('active');
};
closebtn.onclick = function () {
  this.classList.toggle('active');
  document.querySelector('.slideMenu').classList.toggle('active');
};
// ハンバーガーメニュー　ココマデ