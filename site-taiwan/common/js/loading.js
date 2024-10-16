'use strict';

function loaded() {
  document.getElementById('loading').classList.remove('active');
}

window.addEventListener('load', function () {
  setTimeout(loaded, 2000);
});

setTimeout(loaded, 5000);