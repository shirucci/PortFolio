'use strict';

const top_btn = document.getElementById('top_btn');

top_btn.addEventListener("click", scroll_top);
function scroll_top() {
  window.scroll({ top: 0, behavior: "smooth" });
}

window.addEventListener('scroll', () => {
  if (window.scrollY > 200) {
    top_btn.style.display = 'block';
  } else {
    top_btn.style.display = 'none';
  }
});
