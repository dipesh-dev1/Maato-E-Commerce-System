import { request } from '../lib/http.js';

/**
 * Daily revenue line chart in plain SVG: one series, recessive grid, crosshair + tooltip on hover
 * and keyboard (arrow keys), and a 7/30/90-day range switch. A table view sits under the chart.
 */
const NS = 'http://www.w3.org/2000/svg';
const el = (name, attrs = {}) => {
  const node = document.createElementNS(NS, name);
  Object.entries(attrs).forEach(([k, v]) => node.setAttribute(k, v));
  return node;
};
const compact = (n) => (n >= 100000 ? `${(n / 100000).toFixed(n >= 1000000 ? 0 : 1)}L` : n >= 1000 ? `${Math.round(n / 1000)}k` : String(Math.round(n)));
const dateLabel = (iso, opts) => new Date(`${iso}T00:00:00`).toLocaleDateString('en-GB', opts);

export function initChart(root) {
  const svg = root.querySelector('[data-chart-svg]');
  const tip = root.querySelector('[data-chart-tip]');
  const card = root.closest('.a-card');
  let series = JSON.parse(root.dataset.series);
  const pad = { top: 12, right: 12, bottom: 26, left: 44 };

  const niceMax = (max) => {
    if (max <= 0) return 1000;
    const step = 10 ** Math.floor(Math.log10(max));
    return Math.ceil(max / step) * step;
  };

  function draw() {
    const width = svg.clientWidth || 640;
    const height = svg.clientHeight || 256;
    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    svg.replaceChildren();
    const w = width - pad.left - pad.right;
    const h = height - pad.top - pad.bottom;
    const max = niceMax(Math.max(...series.map((p) => p.revenue)));
    const x = (i) => pad.left + (series.length === 1 ? w / 2 : (i / (series.length - 1)) * w);
    const y = (v) => pad.top + h - (v / max) * h;

    const grid = el('g', { class: 'grid' });
    const axis = el('g', { class: 'axis' });
    for (let t = 0; t <= 4; t++) {
      const v = (max / 4) * t;
      grid.append(el('line', { x1: pad.left, x2: width - pad.right, y1: y(v), y2: y(v) }));
      const label = el('text', { x: pad.left - 8, y: y(v) + 4, 'text-anchor': 'end' });
      label.textContent = compact(v);
      axis.append(label);
    }
    // Roughly one date label per 80px, and never one crowding the final label.
    const every = Math.ceil(series.length / Math.max(2, Math.floor(w / 80)));
    series.forEach((p, i) => {
      const last = i === series.length - 1;
      if (!last && (i % every !== 0 || x(series.length - 1) - x(i) < 60)) return;
      const label = el('text', { x: x(i), y: height - 6, 'text-anchor': i === 0 ? 'start' : i === series.length - 1 ? 'end' : 'middle' });
      label.textContent = dateLabel(p.date, { day: 'numeric', month: 'short' });
      axis.append(label);
    });

    const points = series.map((p, i) => `${x(i).toFixed(1)},${y(p.revenue).toFixed(1)}`);
    const area = el('path', { class: 'area', d: `M${x(0)},${y(0)} L${points.join(' L')} L${x(series.length - 1)},${y(0)} Z` });
    const line = el('path', { class: 'line', d: `M${points.join(' L')}` });
    const cross = el('line', { class: 'crosshair', y1: pad.top, y2: pad.top + h, visibility: 'hidden' });
    const dot = el('circle', { class: 'dot', r: 4.5, visibility: 'hidden' });
    const hit = el('rect', { x: pad.left, y: pad.top, width: w, height: h, fill: 'transparent', tabindex: '0', 'aria-label': 'Revenue by day. Use left and right arrow keys to read each day.' });
    svg.append(grid, axis, area, line, cross, dot, hit);

    let active = -1;
    const show = (i) => {
      active = Math.max(0, Math.min(series.length - 1, i));
      const p = series[active];
      cross.setAttribute('x1', x(active)); cross.setAttribute('x2', x(active)); cross.setAttribute('visibility', 'visible');
      dot.setAttribute('cx', x(active)); dot.setAttribute('cy', y(p.revenue)); dot.setAttribute('visibility', 'visible');
      tip.hidden = false;
      tip.replaceChildren();
      const strong = document.createElement('strong');
      strong.textContent = p.label;
      tip.append(strong, `${dateLabel(p.date, { weekday: 'short', day: 'numeric', month: 'short' })} · ${p.orders} ${p.orders === 1 ? 'order' : 'orders'}`);
      const scale = svg.getBoundingClientRect().width / width;
      const left = Math.min(Math.max(x(active) * scale, 70), svg.getBoundingClientRect().width - 70);
      tip.style.left = `${left}px`;
      tip.style.top = `${Math.max(0, y(p.revenue) * scale - 58)}px`;
    };
    const hide = () => { tip.hidden = true; cross.setAttribute('visibility', 'hidden'); dot.setAttribute('visibility', 'hidden'); };
    hit.addEventListener('pointermove', (e) => {
      const rect = svg.getBoundingClientRect();
      const px = (e.clientX - rect.left) * (width / rect.width);
      show(Math.round(((px - pad.left) / w) * (series.length - 1)));
    });
    hit.addEventListener('pointerleave', hide);
    hit.addEventListener('blur', hide);
    hit.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
        e.preventDefault();
        show(active < 0 ? series.length - 1 : active + (e.key === 'ArrowRight' ? 1 : -1));
      }
    });
  }

  const table = card.querySelector('[data-chart-table] tbody');
  const renderTable = () => {
    table.replaceChildren(...[...series].reverse().map((p) => {
      const tr = document.createElement('tr');
      [dateLabel(p.date, { weekday: 'short', day: 'numeric', month: 'short' }), String(p.orders), p.label].forEach((text, i) => {
        const td = document.createElement('td');
        td.textContent = text;
        if (i) td.className = 'num';
        tr.append(td);
      });
      return tr;
    }));
  };

  card.querySelectorAll('[data-chart-range]').forEach((button) => button.addEventListener('click', async () => {
    card.querySelectorAll('[data-chart-range]').forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
    const res = await request(`${root.dataset.endpoint}?days=${button.dataset.chartRange}`);
    if (!res.ok) return;
    series = res.data.series;
    card.querySelector('[data-chart-summary]').textContent = `${res.data.total} from ${res.data.orders} ${res.data.orders === 1 ? 'order' : 'orders'}`;
    draw();
    renderTable();
  }));

  new ResizeObserver(() => draw()).observe(svg);
  draw();
}
