'use strict';

const show_modal = document.querySelectorAll('.modalShow');
show_modal.forEach(function (btn) {
  btn.onclick = function () {
    let modal = btn.getAttribute('data-modal');
    document.getElementById(modal).style.display = 'block';
  };
});

const close_btn = document.querySelectorAll('.modalClose');
close_btn.forEach(function (btn) {
  btn.onclick = function () {
    let modal = btn.closest('.modalCont');
    modal.style.display = 'none';
  };
});