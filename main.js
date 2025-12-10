document.addEventListener('DOMContentLoaded', () => {
  /* ------------------------------
     IMAGE LIGHTBOX
  -------------------------------- */
  const gallery = document.getElementById('gallery');
  const lb = document.getElementById('lb');
  const lbImg = document.getElementById('lbImg');
  const closeBtn = document.getElementById('close');

  if (gallery && lb && lbImg && closeBtn) {
    gallery.addEventListener('click', e => {
      const card = e.target.closest('.card');
      if (!card) return;

      // only lightbox if click is on image, not on link to product page
      // comment out preventDefault if you prefer to always go to product.php
      e.preventDefault();

      const full = card.dataset.full;
      const title = card.dataset.title || '';

      if (!full) return;

      lbImg.src = full;
      lbImg.alt = title;

      lb.classList.add('open');
      lb.setAttribute('aria-hidden', 'false');
    });

    closeBtn.addEventListener('click', () => {
      lb.classList.remove('open');
      lb.setAttribute('aria-hidden', 'true');
      lbImg.src = '';
    });

    lb.addEventListener('click', e => {
      if (e.target === e.currentTarget) {
        closeBtn.click();
      }
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        closeBtn.click();
      }
    });
  }

  /* ------------------------------
     SNOWFALL ANIMATION
  -------------------------------- */
  const snowContainer = document.getElementById('snow-container');

  if (snowContainer) {
    const FLAKES = 80;

    for (let i = 0; i < FLAKES; i++) {
      const flake = document.createElement('div');
      flake.className = 'snowflake';

      const size = 2 + Math.random() * 4;
      flake.style.width = size + 'px';
      flake.style.height = size + 'px';

      flake.style.left = Math.random() * 100 + 'vw';
      flake.style.animationDuration = 8 + Math.random() * 10 + 's';
      flake.style.animationDelay = Math.random() * 10 + 's';
      flake.style.setProperty('--x-move', (Math.random() * 40 - 20) + 'px');
      flake.style.opacity = 0.4 + Math.random() * 0.6;

      snowContainer.appendChild(flake);
    }
  }
});
