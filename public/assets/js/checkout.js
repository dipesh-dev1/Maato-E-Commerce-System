/** Delivery vs pickup toggles the right step, and totals update from pre-computed quotes. */
export function initCheckout() {
  const form = document.querySelector('[data-checkout]');
  if (!form) return;
  const quotes = JSON.parse(form.dataset.quotes || '{}');
  const shipping = form.querySelector('[data-total-shipping]');
  const grand = form.querySelector('[data-total-grand]');
  const buttonTotal = form.querySelector('[data-total-button]');

  const update = () => {
    const pickup = form.querySelector('[data-method]:checked')?.value === 'pay_on_pickup';
    form.querySelector('[data-delivery-only]').hidden = pickup;
    form.querySelector('[data-pickup-only]').hidden = !pickup;
    const key = pickup ? 'pickup' : form.querySelector('[data-address]:checked')?.value;
    const quote = quotes[key];
    if (!quote) return;
    shipping.textContent = quote.shipping;
    grand.textContent = quote.total;
    buttonTotal.textContent = quote.total;
  };
  form.addEventListener('change', update);
  update();

  form.addEventListener('submit', (e) => {
    const button = form.querySelector('[data-place-order]');
    if (button.classList.contains('is-loading')) { e.preventDefault(); return; }
    button.classList.add('is-loading');
  });
}
