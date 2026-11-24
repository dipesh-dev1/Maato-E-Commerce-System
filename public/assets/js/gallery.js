/** Thumbnail rail + swipeable stage. Thumbs and dots follow the scroll position. */
export function initGallery(root) {
  const stage = root.querySelector('[data-gallery-stage]');
  if (!stage) return;
  const thumbs = [...root.querySelectorAll('[data-gallery-thumb]')];
  const dots = [...root.querySelectorAll('.gallery__dots span')];
  const smooth = matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';

  const setActive = (index) => {
    thumbs.forEach((t, i) => t.setAttribute('aria-current', String(i === index)));
    dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
  };

  thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => {
    stage.scrollTo({ left: stage.clientWidth * i, behavior: smooth });
    setActive(i);
  }));

  let frame;
  stage.addEventListener('scroll', () => {
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(() => setActive(Math.round(stage.scrollLeft / stage.clientWidth)));
  }, { passive: true });

  stage.addEventListener('keydown', (e) => {
    if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
    e.preventDefault();
    const dir = e.key === 'ArrowRight' ? 1 : -1;
    stage.scrollBy({ left: dir * stage.clientWidth, behavior: smooth });
  });
}
