/** Sticky header that condenses on scroll, and the keyboard-friendly mega menu. */
export function initHeader() {
  const header = document.querySelector('[data-header]');
  if (!header) return;

  let ticking = false;
  const update = () => {
    header.classList.toggle('is-condensed', window.scrollY > 40);
    ticking = false;
  };
  window.addEventListener('scroll', () => {
    if (!ticking) { requestAnimationFrame(update); ticking = true; }
  }, { passive: true });
  update();

  const trigger = header.querySelector('[data-mega-trigger]');
  const panel = header.querySelector('[data-mega]');
  if (!trigger || !panel) return;

  let closeTimer;
  const setOpen = (open) => {
    clearTimeout(closeTimer);
    panel.hidden = !open;
    trigger.setAttribute('aria-expanded', String(open));
  };
  const isOpen = () => !panel.hidden;
  const canHover = matchMedia('(hover: hover) and (pointer: fine)');

  trigger.addEventListener('click', () => setOpen(!isOpen()));
  trigger.addEventListener('pointerenter', () => { if (canHover.matches) setOpen(true); });
  [trigger, panel].forEach((el) => el.addEventListener('pointerleave', (e) => {
    if (!canHover.matches || panel.contains(e.relatedTarget) || trigger.contains(e.relatedTarget)) return;
    closeTimer = setTimeout(() => setOpen(false), 180);
  }));
  panel.addEventListener('pointerenter', () => clearTimeout(closeTimer));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen()) { setOpen(false); trigger.focus(); }
  });
  document.addEventListener('click', (e) => {
    if (isOpen() && !panel.contains(e.target) && !trigger.contains(e.target)) setOpen(false);
  });
  panel.addEventListener('focusout', (e) => {
    if (!panel.contains(e.relatedTarget) && e.relatedTarget !== trigger) setOpen(false);
  });
  trigger.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowDown') { e.preventDefault(); setOpen(true); panel.querySelector('a')?.focus(); }
  });
}
