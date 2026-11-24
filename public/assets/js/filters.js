import { request, debounce } from './lib/http.js';
import { toast } from './lib/toast.js';

/** Listing filters, sort and pagination update the grid in place and keep the URL shareable. */
export function initFilters() {
  const form = document.querySelector('[data-filter-form]');
  const results = document.querySelector('[data-listing-results]');
  if (!form || !results) return;
  const count = document.querySelector('[data-result-count]');
  const sort = document.querySelector('[data-sort-select]');
  const details = document.querySelector('[data-filters]');
  const mobile = matchMedia('(max-width: 860px)');

  const syncDisclosure = () => { details.open = !mobile.matches; };
  syncDisclosure();
  mobile.addEventListener('change', syncDisclosure);

  const urlFor = (page = null) => {
    const params = new URLSearchParams();
    for (const [key, value] of new FormData(form)) {
      if (value !== '' && !(key === 'sort' && value === 'featured')) params.set(key, value);
    }
    if (page && page > 1) params.set('page', page);
    const qs = params.toString();
    return form.action + (qs ? `?${qs}` : '');
  };

  let controller = 0;
  const load = async (url, push = true) => {
    const ticket = ++controller;
    results.classList.add('is-loading');
    results.setAttribute('aria-busy', 'true');
    const res = await request(url);
    if (ticket !== controller) return; // a newer request superseded this one
    results.classList.remove('is-loading');
    results.removeAttribute('aria-busy');
    if (!res.ok) { toast(res.error, 'error'); return; }
    results.innerHTML = res.data.html;
    count.textContent = `${res.data.total} ${res.data.total === 1 ? 'piece' : 'pieces'}`;
    if (push) history.pushState({ listing: true }, '', url);
  };

  const refresh = () => load(urlFor());
  form.addEventListener('change', (e) => { if (!e.target.matches('input[type="number"], input[type="search"]')) refresh(); });
  form.addEventListener('input', debounce((e) => { if (e.target.matches('input[type="number"], input[type="search"]')) refresh(); }, 500));
  form.addEventListener('submit', (e) => { e.preventDefault(); refresh(); if (mobile.matches) details.open = false; });

  sort?.addEventListener('change', () => {
    form.querySelector('[data-sort-mirror]').value = sort.value;
    refresh();
  });

  results.addEventListener('click', (e) => {
    const link = e.target.closest('.pagination a[data-page]');
    if (!link) return;
    e.preventDefault();
    load(urlFor(Number(link.dataset.page))).then(() => {
      results.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
    });
  });

  window.addEventListener('popstate', () => load(window.location.href, false));
}
