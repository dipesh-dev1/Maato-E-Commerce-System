import { request, baseUrl } from './lib/http.js';
import { openDialog, closeDialog } from './lib/dialog.js';
import { toast } from './lib/toast.js';
import { initPurchaseForms } from './purchase.js';

export function initQuickView() {
  const dialog = document.getElementById('quick-view');
  if (!dialog) return;
  const body = dialog.querySelector('[data-quick-view-body]');
  const skeleton = body.innerHTML;

  document.addEventListener('click', async (e) => {
    const trigger = e.target.closest('[data-quick-view]');
    if (!trigger) return;
    body.innerHTML = skeleton;
    openDialog(dialog, trigger);
    const res = await request(baseUrl(`/product/${trigger.dataset.quickView}/quick-view`));
    if (!res.ok) {
      closeDialog(dialog);
      toast(res.error, 'error');
      return;
    }
    body.innerHTML = res.data.html;
    initPurchaseForms(body);
    body.querySelector('h2')?.setAttribute('tabindex', '-1');
    body.querySelector('h2')?.focus();
  });
}
