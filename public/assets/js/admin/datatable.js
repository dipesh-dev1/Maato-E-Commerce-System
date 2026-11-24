import { request, debounce } from '../lib/http.js';
import { toast } from '../lib/toast.js';

/**
 * Enhances a server-rendered table: filters, search, sort headers and pagination re-fetch only the
 * table body; bulk checkboxes drive the bulk bar. Without JS, the same links and forms still work.
 */
export function initDataTable(root) {
  const filters = root.querySelector('[data-table-filters]');
  const body = root.querySelector('[data-table-body]');
  const bulkForm = root.querySelector('[data-bulk-form]');
  if (!body) return;

  let ticket = 0;
  const load = async (url, push = true) => {
    const mine = ++ticket;
    body.classList.add('is-loading');
    const res = await request(url);
    if (mine !== ticket) return;
    body.classList.remove('is-loading');
    if (!res.ok) { toast(res.error, 'error'); return; }
    body.innerHTML = res.data.html;
    syncBulk();
    if (push) history.replaceState(null, '', url);
    const params = new URL(url, location.href).searchParams;
    if (filters) {
      ['sort', 'dir'].forEach((key) => {
        const input = filters.querySelector(`input[name="${key}"]`);
        if (input) input.value = params.get(key) ?? '';
      });
      const exportLink = document.querySelector('[data-export-link]');
      if (exportLink) {
        const exportUrl = new URL(exportLink.href);
        exportUrl.search = new URLSearchParams([...params].filter(([k]) => k !== 'page')).toString();
        exportLink.href = exportUrl.toString();
      }
    }
  };

  const filterUrl = () => {
    const params = new URLSearchParams();
    for (const [key, value] of new FormData(filters)) if (value !== '') params.set(key, value);
    return `${filters.action}?${params}`;
  };

  if (filters) {
    filters.addEventListener('change', (e) => { if (e.target.name !== 'q') load(filterUrl()); });
    filters.addEventListener('input', debounce((e) => { if (e.target.name === 'q') load(filterUrl()); }, 300));
    filters.addEventListener('submit', (e) => { e.preventDefault(); load(filterUrl()); });
  }

  body.addEventListener('click', (e) => {
    const link = e.target.closest('[data-table-link], .pagination a');
    if (!link) return;
    e.preventDefault();
    load(link.href);
  });

  // ---- Bulk selection ----
  const bar = root.querySelector('[data-bulk-bar]');
  const syncBulk = () => {
    if (!bar) return;
    const checks = [...body.querySelectorAll('[data-row-check]')];
    const selected = checks.filter((c) => c.checked);
    checks.forEach((c) => c.closest('tr').classList.toggle('is-selected', c.checked));
    bar.hidden = selected.length === 0;
    bar.querySelector('[data-bulk-count]').textContent = `${selected.length} selected`;
    const all = body.querySelector('[data-select-all]');
    if (all) {
      all.checked = selected.length > 0 && selected.length === checks.length;
      all.indeterminate = selected.length > 0 && selected.length < checks.length;
    }
  };
  body.addEventListener('change', (e) => {
    if (e.target.matches('[data-select-all]')) {
      body.querySelectorAll('[data-row-check]').forEach((c) => { c.checked = e.target.checked; });
    }
    if (e.target.matches('[data-select-all], [data-row-check]')) syncBulk();
  });

  // Destructive bulk actions confirm first; the confirm dialog only intercepts forms with data-confirm.
  if (bulkForm?.dataset.confirmWhen) {
    const message = bulkForm.dataset.confirm;
    const sync = () => {
      const destructive = bulkForm.querySelector('[name="action"]').value === bulkForm.dataset.confirmWhen;
      if (destructive) bulkForm.dataset.confirm = message;
      else delete bulkForm.dataset.confirm;
    };
    bulkForm.querySelector('[name="action"]').addEventListener('change', sync);
    sync();
  }
  syncBulk();
}
