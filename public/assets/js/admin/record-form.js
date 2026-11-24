import { submitForm } from '../lib/http.js';
import { openDialog } from '../lib/dialog.js';
import { toast } from '../lib/toast.js';

/**
 * Slide-over create/edit forms. "New" clears the form; "Edit" fills it from the row's data-record
 * JSON and points it at the update URL. Submission goes over fetch so validation errors appear
 * inline without losing what was typed.
 */
function fill(form, record) {
  form.reset();
  Object.entries(record).forEach(([name, value]) => {
    form.querySelectorAll(`[name="${name}"]`).forEach((field) => {
      if (field.type === 'checkbox') field.checked = Boolean(value);
      else if (field.type === 'radio') field.checked = field.value === String(value);
      else field.value = value ?? '';
    });
  });
}

function clearErrors(form) {
  form.querySelectorAll('[data-error-for]').forEach((el) => { el.hidden = true; el.textContent = ''; });
  form.querySelectorAll('[aria-invalid]').forEach((el) => { el.removeAttribute('aria-invalid'); el.removeAttribute('aria-describedby'); });
}

function showErrors(form, errors, fallback) {
  let first = null;
  Object.entries(errors ?? {}).forEach(([name, message]) => {
    const slot = form.querySelector(`[data-error-for="${name}"]`);
    const field = form.querySelector(`[name="${name}"]`);
    if (slot) { slot.textContent = message; slot.hidden = false; }
    if (field) {
      field.setAttribute('aria-invalid', 'true');
      if (slot) field.setAttribute('aria-describedby', slot.id);
      first ??= field;
    }
    if (!slot) toast(message, 'error');
  });
  if (!errors) toast(fallback, 'error');
  first?.focus();
}

export function initRecordForms() {
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-new-record], [data-edit-record]');
    if (!trigger) return;
    const dialog = document.getElementById(trigger.dataset.newRecord ?? trigger.dataset.editRecord);
    const form = dialog.querySelector('[data-record-form]');
    const editing = 'editRecord' in trigger.dataset;
    clearErrors(form);
    if (editing) {
      fill(form, JSON.parse(trigger.dataset.record));
      form.action = form.dataset.update.replace('__ID__', trigger.dataset.id);
    } else {
      form.reset();
      form.action = form.dataset.create;
    }
    form.querySelector('[data-method-field]').value = editing ? 'PATCH' : 'POST';
    form.querySelectorAll('[data-slug-target]').forEach((el) => { el.dataset.auto = editing ? '' : '1'; });
    const title = dialog.querySelector('[data-slideover-title]');
    title.textContent = editing ? title.dataset.edit : title.dataset.new;
    openDialog(dialog, trigger);
    form.querySelector('input:not([type="hidden"]), select, textarea')?.focus();
  });

  document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!form.matches('[data-record-form]')) return;
    e.preventDefault();
    const button = form.querySelector('[type="submit"]');
    button.classList.add('is-loading');
    clearErrors(form);
    const res = await submitForm(form);
    button.classList.remove('is-loading');
    if (res.ok && res.data?.redirect) {
      window.location.href = res.data.redirect;
      return;
    }
    showErrors(form, res.errors, res.error);
  });
}

/** Typing a name fills the slug until the slug is edited by hand. */
export function initSlugFields() {
  const slugify = (text) => text.normalize('NFKD').replace(/[̀-ͯ]/g, '').toLowerCase()
    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
  document.addEventListener('input', (e) => {
    if (e.target.matches('[data-slug-source]')) {
      const target = e.target.form?.querySelector('[data-slug-target]');
      if (target && target.dataset.auto === '1') target.value = slugify(e.target.value);
    } else if (e.target.matches('[data-slug-target]')) {
      e.target.dataset.auto = '';
    }
  });
}
