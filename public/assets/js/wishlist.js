import { submitForm, baseUrl } from './lib/http.js';
import { toast } from './lib/toast.js';

async function toggle(form, submitter, button) {
  button.disabled = true;
  const res = await submitForm(form, submitter);
  button.disabled = false;
  if (res.status === 401) {
    window.location.href = baseUrl('/login');
    return;
  }
  if (!res.ok) {
    toast(res.error, 'error');
    return;
  }
  button.setAttribute('aria-pressed', String(res.data.saved));
  toast(res.data.message, 'success');
}

export function initWishlist() {
  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.matches('[data-wishlist-form]')) {
      e.preventDefault();
      toggle(form, null, form.querySelector('button'));
    } else if (e.submitter?.matches('[data-wishlist-button]')) {
      e.preventDefault();
      toggle(form, e.submitter, e.submitter);
    }
  });
}
