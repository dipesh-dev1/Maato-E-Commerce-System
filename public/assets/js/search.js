import { request, debounce, baseUrl } from './lib/http.js';
import { openDialog } from './lib/dialog.js';

/** Header search with instant suggestions (combobox pattern: arrows move, Enter opens). */
export function initSearch() {
  const dialog = document.getElementById('search-dialog');
  if (!dialog) return;
  const input = dialog.querySelector('[data-search-input]');
  const list = dialog.querySelector('[data-search-results]');
  const hint = list.innerHTML;
  let active = -1;

  const open = (opener) => { openDialog(dialog, opener); input.select(); };
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-open-search]');
    if (opener) { e.preventDefault(); open(opener); }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === '/' && !e.target.closest('input, textarea, select, [contenteditable]') && !document.querySelector('dialog[open]')) {
      e.preventDefault();
      open(null);
    }
  });

  const options = () => [...list.querySelectorAll('[role="option"]')];
  const highlight = (text, query) => {
    const frag = document.createDocumentFragment();
    const i = text.toLowerCase().indexOf(query.toLowerCase());
    if (i < 0 || !query) { frag.append(text); return frag; }
    const mark = document.createElement('mark');
    mark.textContent = text.slice(i, i + query.length);
    frag.append(text.slice(0, i), mark, text.slice(i + query.length));
    return frag;
  };

  const render = ({ items, query, all_url: allUrl }) => {
    active = -1;
    input.removeAttribute('aria-activedescendant');
    if (!items.length) {
      list.innerHTML = '';
      const p = document.createElement('p');
      p.className = 'search-results__hint';
      p.textContent = `Nothing called “${query}” yet. Try a material, like linen, oak or cane.`;
      list.append(p);
      input.setAttribute('aria-expanded', 'false');
      return;
    }
    list.innerHTML = '';
    items.forEach((item, i) => {
      const a = document.createElement('a');
      a.className = 'suggestion';
      a.href = item.url;
      a.id = `suggestion-${i}`;
      a.setAttribute('role', 'option');
      a.setAttribute('aria-selected', 'false');
      const img = Object.assign(document.createElement('img'), { src: item.image, alt: '', width: 56, height: 70 });
      const text = document.createElement('span');
      const name = document.createElement('span');
      name.className = 'suggestion__name';
      name.append(highlight(item.name, query));
      const meta = document.createElement('span');
      meta.className = 'suggestion__meta';
      meta.textContent = item.category;
      text.append(name, document.createElement('br'), meta);
      const price = document.createElement('span');
      price.className = 'suggestion__price num';
      price.textContent = item.price;
      if (item.was) { const s = document.createElement('s'); s.textContent = item.was; price.append(s); }
      a.append(img, text, price);
      list.append(a);
    });
    const all = document.createElement('a');
    all.className = 'search-results__all';
    all.href = allUrl;
    all.textContent = `See all results for “${query}”`;
    list.append(all);
    input.setAttribute('aria-expanded', 'true');
  };

  const suggest = debounce(async () => {
    const q = input.value.trim();
    if (q.length < 2) { list.innerHTML = hint; input.setAttribute('aria-expanded', 'false'); return; }
    const res = await request(baseUrl(`/search/suggest?q=${encodeURIComponent(q)}`));
    if (res.ok && input.value.trim() === q) render(res.data);
  }, 180);
  input.addEventListener('input', suggest);

  input.addEventListener('keydown', (e) => {
    const opts = options();
    if (!opts.length || !['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) return;
    if (e.key === 'Enter') {
      if (active >= 0) { e.preventDefault(); opts[active].click(); }
      return;
    }
    e.preventDefault();
    active = (active + (e.key === 'ArrowDown' ? 1 : -1) + opts.length) % opts.length;
    opts.forEach((o, i) => o.setAttribute('aria-selected', String(i === active)));
    input.setAttribute('aria-activedescendant', opts[active].id);
    opts[active].scrollIntoView({ block: 'nearest' });
  });
}
