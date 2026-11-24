const ICONS = { success: 'check-circle', error: 'alert', info: 'info' };
const LIFETIME = 5000;

const spriteUrl = () => document.querySelector('svg.icon use')?.getAttribute('href')?.split('#')[0] ?? '';

function dismiss(el) {
  if (!el || el.classList.contains('is-leaving')) return;
  el.classList.add('is-leaving');
  el.addEventListener('animationend', () => el.remove(), { once: true });
  setTimeout(() => el.remove(), 400);
}

export function toast(message, type = 'success') {
  const region = document.querySelector('[data-toasts]');
  const template = document.getElementById('toast-template');
  if (!region || !template || !message) return;
  const el = template.content.firstElementChild.cloneNode(true);
  el.classList.add(`toast--${type}`);
  el.querySelector('use').setAttribute('href', `${spriteUrl()}#i-${ICONS[type] ?? 'info'}`);
  el.querySelector('p').textContent = message;
  region.append(el);
  setTimeout(() => dismiss(el), type === 'error' ? LIFETIME * 1.6 : LIFETIME);
}

export function initToasts() {
  document.querySelectorAll('[data-toast]').forEach((el) => setTimeout(() => dismiss(el), LIFETIME));
  document.addEventListener('click', (e) => {
    const close = e.target.closest('[data-toast-close]');
    if (close) dismiss(close.closest('[data-toast]'));
  });
}
