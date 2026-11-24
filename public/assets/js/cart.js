import { request, submitForm, debounce, baseUrl } from './lib/http.js';
import { toast } from './lib/toast.js';
import { openDialog, closeDialog } from './lib/dialog.js';

const drawer = () => document.getElementById('cart-drawer');

export function renderCart(data) {
  if (!data) return;
  const drawerBody = document.querySelector('[data-cart-drawer-body]');
  const pageBody = document.querySelector('[data-cart-page]');
  if (drawerBody && data.drawer !== undefined) drawerBody.innerHTML = data.drawer;
  if (pageBody && data.page !== undefined) pageBody.innerHTML = data.page;
  document.querySelectorAll('[data-cart-count]').forEach((bubble) => {
    const changed = bubble.textContent !== String(data.count);
    bubble.textContent = String(data.count);
    bubble.hidden = !data.count;
    bubble.closest('a')?.setAttribute('aria-label', `Bag, ${data.count} items`);
    if (changed) {
      bubble.classList.remove('is-bumped');
      void bubble.offsetWidth;
      bubble.classList.add('is-bumped');
    }
  });
}

async function openCart(opener) {
  const dialog = drawer();
  if (!dialog) return;
  openDialog(dialog, opener);
  const res = await request(baseUrl('/cart/drawer'));
  if (res.ok) renderCart(res.data);
}

async function addToCart(form, submitter) {
  submitter?.classList.add('is-loading');
  const res = await submitForm(form);
  submitter?.classList.remove('is-loading');
  renderCart(res.data);
  if (!res.ok) {
    toast(res.error, 'error');
    return;
  }
  toast(res.data.message, 'success');
  const quick = form.closest('dialog');
  if (quick) closeDialog(quick);
  if (!document.querySelector('[data-cart-page]')) openDialog(drawer(), submitter);
}

async function mutate(form, line) {
  line?.classList.add('is-busy');
  const res = await submitForm(form);
  line?.classList.remove('is-busy');
  renderCart(res.data);
  if (!res.ok) toast(res.error, 'error');
  else if (form.matches('[data-cart-coupon]')) toast(res.data.message, 'success');
}

export function initCart() {
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-open-cart]');
    if (!opener) return;
    e.preventDefault();
    openCart(opener);
  });

  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.matches('[data-purchase-form]') && !e.submitter?.matches('[data-wishlist-button]')) {
      e.preventDefault();
      addToCart(form, e.submitter);
    } else if (form.matches('[data-cart-remove], [data-cart-coupon], [data-cart-update]')) {
      e.preventDefault();
      mutate(form, form.closest('[data-cart-line]'));
    }
  });

  // Quantity edits are batched so tapping + three times sends one request.
  const queue = new WeakMap();
  document.addEventListener('change', (e) => {
    const form = e.target.closest('[data-cart-update]');
    if (!form) return;
    if (!queue.has(form)) queue.set(form, debounce(() => mutate(form, form.closest('[data-cart-line]')), 450));
    queue.get(form)();
  });
}
