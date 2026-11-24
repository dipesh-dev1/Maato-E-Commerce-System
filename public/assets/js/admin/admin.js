import { initDialogs, initConfirm } from '../lib/dialog.js';
import { initToasts } from '../lib/toast.js';
import { initRecordForms, initSlugFields } from './record-form.js';
import { initDataTable } from './datatable.js';

document.documentElement.classList.replace('no-js', 'js');

initDialogs();
initConfirm();
initToasts();
initRecordForms();
initSlugFields();
document.querySelectorAll('[data-table]').forEach(initDataTable);

const sidebar = document.querySelector('[data-sidebar]');
const toggle = document.querySelector('[data-sidebar-toggle]');
toggle?.addEventListener('click', () => {
  const open = sidebar.classList.toggle('is-open');
  toggle.setAttribute('aria-expanded', String(open));
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && sidebar?.classList.contains('is-open')) {
    sidebar.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.focus();
  }
});

const productForm = document.querySelector('[data-product-form]');
if (productForm) import('./product-form.js').then((m) => m.initProductForm(productForm));
const chart = document.querySelector('[data-chart]');
if (chart) import('./chart.js').then((m) => m.initChart(chart));
