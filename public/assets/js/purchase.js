/**
 * Live variant picker. The form works without this (the server resolves size + colour); with it,
 * price, stock, SKU and availability update as options change, and the exact variant id is sent.
 */
export function initPurchaseForm(form) {
  if (form.dataset.ready) return;
  form.dataset.ready = '1';
  const variants = JSON.parse(form.dataset.variants || '[]');
  const lowStock = Number(form.dataset.lowStock || 5);
  const hidden = Object.assign(document.createElement('input'), { type: 'hidden', name: 'variant_id' });
  form.append(hidden);

  const selected = (name) => form.querySelector(`input[name="${name}"]:checked`)?.value ?? null;
  const match = (size, color) => variants.find((v) => (v.size ?? null) === size && (v.color ?? null) === color);

  const markAvailability = () => {
    form.querySelectorAll('[data-option]').forEach((group) => {
      const name = group.dataset.option;
      group.querySelectorAll('input').forEach((input) => {
        const size = name === 'size' ? input.value : selected('size');
        const color = name === 'color' ? input.value : selected('color');
        const v = match(size, color);
        input.closest('.option__choice').classList.toggle('is-unavailable', !v || v.stock <= 0);
      });
      const label = group.querySelector('[data-option-label]');
      if (label) label.textContent = selected(name) ?? '';
    });
  };

  const render = () => {
    const v = match(selected('size'), selected('color'));
    const button = form.querySelector('[data-add-button]');
    const note = form.querySelector('[data-stock-note]');
    markAvailability();
    if (!v) {
      hidden.value = '';
      button.disabled = true;
      button.textContent = 'Not available';
      note.className = 'stock-note is-out';
      note.textContent = "That combination isn't made. Try another size or colour.";
      return;
    }
    hidden.value = String(v.id);
    form.querySelector('[data-price-now]').textContent = v.price;
    form.querySelector('[data-price-now]').classList.toggle('is-sale', Boolean(v.was));
    const was = form.querySelector('[data-price-was]');
    was.hidden = !v.was;
    was.querySelector('span:last-child').textContent = v.was ?? '';
    const off = form.querySelector('[data-price-off]');
    off.hidden = !v.off;
    off.querySelector('span').textContent = String(v.off);
    form.querySelector('[data-sku]').textContent = v.sku;

    const qty = form.querySelector('input[name="quantity"]');
    qty.max = String(Math.max(1, Math.min(10, v.stock)));
    if (Number(qty.value) > Number(qty.max)) qty.value = qty.max;

    button.disabled = v.stock <= 0;
    button.textContent = v.stock <= 0 ? 'Sold out' : 'Add to bag';
    note.className = 'stock-note' + (v.stock <= 0 ? ' is-out' : v.stock <= lowStock ? ' is-low' : '');
    note.textContent = v.stock <= 0
      ? 'Sold out in this option. Try another, or check back soon.'
      : v.stock <= lowStock ? `Only ${v.stock} left. Ships in 1–2 days.` : 'In stock. Ships in 1–2 days.';
  };

  form.addEventListener('change', (e) => { if (e.target.matches('[data-option] input')) render(); });
  render();
}

export function initPurchaseForms(root = document) {
  root.querySelectorAll('[data-purchase-form]').forEach(initPurchaseForm);
}
