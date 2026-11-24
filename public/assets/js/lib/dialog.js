// Native <dialog> gives us focus trapping, Escape and inert background; this adds exit animations,
// backdrop clicks and declarative open/close buttons.
const reduceMotion = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

export function openDialog(dialog, opener = document.activeElement) {
  if (!dialog || dialog.open) return;
  dialog._opener = opener;
  dialog.showModal();
  document.body.classList.add('has-dialog');
}

export function closeDialog(dialog) {
  if (!dialog?.open || dialog.classList.contains('is-closing')) return;
  const finish = () => {
    dialog.classList.remove('is-closing');
    if (dialog.open) dialog.close();
  };
  if (reduceMotion()) return finish();
  dialog.classList.add('is-closing');
  dialog.addEventListener('animationend', (e) => { if (e.target === dialog) finish(); }, { once: true });
  setTimeout(finish, 320);
}

export function initDialogs() {
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-open-dialog]');
    if (opener) {
      e.preventDefault();
      openDialog(document.getElementById(opener.dataset.openDialog), opener);
      return;
    }
    const closer = e.target.closest('[data-close-dialog]');
    if (closer) {
      closeDialog(closer.closest('dialog'));
      return;
    }
    if (e.target instanceof HTMLDialogElement && e.target.open) {
      const r = e.target.getBoundingClientRect();
      const inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
      if (!inside) closeDialog(e.target);
    }
  });
  // Escape: animate out instead of the instant native close.
  document.addEventListener('cancel', (e) => {
    if (e.target instanceof HTMLDialogElement) {
      e.preventDefault();
      closeDialog(e.target);
    }
  }, true);
  document.addEventListener('close', (e) => {
    if (!document.querySelector('dialog[open]')) document.body.classList.remove('has-dialog');
    e.target._opener?.focus?.();
  }, true);
}

/** Forms with data-confirm="Question?" ask in a styled dialog before submitting. */
export function initConfirm() {
  const dialog = document.getElementById('confirm-dialog');
  if (!dialog) return;
  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form.matches('[data-confirm]') || form.dataset.confirmed === '1') return;
    e.preventDefault();
    e.stopImmediatePropagation();
    dialog.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
    const ok = dialog.querySelector('[data-confirm-ok]');
    if (form.dataset.confirmLabel) ok.textContent = form.dataset.confirmLabel;
    dialog.returnValue = '';
    dialog.addEventListener('close', () => {
      if (dialog.returnValue !== 'confirm') return;
      form.dataset.confirmed = '1';
      form.requestSubmit(e.submitter ?? undefined);
      delete form.dataset.confirmed;
    }, { once: true });
    openDialog(dialog, e.submitter);
  }, true);
}
