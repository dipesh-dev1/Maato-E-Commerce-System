import { initDialogs, initConfirm } from './lib/dialog.js';
import { initToasts } from './lib/toast.js';
import { initQuantitySteppers } from './lib/qty.js';
import { initHeader } from './header.js';
import { initCart } from './cart.js';
import { initWishlist } from './wishlist.js';
import { initReveal } from './reveal.js';

document.documentElement.classList.replace('no-js', 'js');

initDialogs();
initConfirm();
initToasts();
initQuantitySteppers();
initHeader();
initCart();
initWishlist();
initReveal();

document.addEventListener('click', (e) => {
  const toggle = e.target.closest('[data-toggle-password]');
  if (!toggle) return;
  const input = document.getElementById(toggle.getAttribute('aria-controls'));
  const show = input.type === 'password';
  input.type = show ? 'text' : 'password';
  toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
});

// Page-specific behaviour is only downloaded when the page needs it.
const lazy = [
  ['[data-search-form]', () => import('./search.js').then((m) => m.initSearch())],
  ['#quick-view', () => import('./quickview.js').then((m) => m.initQuickView())],
  ['[data-purchase-form]', () => import('./purchase.js').then((m) => m.initPurchaseForms())],
  ['[data-gallery]', () => import('./gallery.js').then((m) => document.querySelectorAll('[data-gallery]').forEach(m.initGallery))],
  ['[data-filter-form]', () => import('./filters.js').then((m) => m.initFilters())],
  ['[data-checkout]', () => import('./checkout.js').then((m) => m.initCheckout())],
];
lazy.forEach(([selector, load]) => { if (document.querySelector(selector)) load(); });
