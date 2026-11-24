/** +/- buttons around a number input. Fires a bubbling "change" so listeners treat it like typing. */
export function initQuantitySteppers() {
  document.addEventListener('click', (e) => {
    const button = e.target.closest('[data-qty-step]');
    if (!button) return;
    const input = button.closest('[data-qty]')?.querySelector('input');
    if (!input) return;
    const min = Number(input.min || 0);
    const max = Number(input.max || 99);
    const next = Math.min(max, Math.max(min, (Number(input.value) || 0) + Number(button.dataset.qtyStep)));
    if (next === Number(input.value)) return;
    input.value = String(next);
    input.dispatchEvent(new Event('change', { bubbles: true }));
  });
}
