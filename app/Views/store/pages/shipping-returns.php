<?php partial('partials/breadcrumb', ['crumbs' => [['Delivery & returns', null]]]); ?>
<div class="container">
  <header class="page-hero">
    <h1 class="h1">Delivery &amp; returns</h1>
    <p>Plain answers to the questions we're asked most. If yours isn't here, <a href="<?= e(route('page', ['page' => 'contact'])) ?>">get in touch</a>.</p>
  </header>
  <div class="page-body">
    <aside class="panel stack" aria-label="At a glance">
      <p class="eyebrow">At a glance</p>
      <p><strong>1–2 days</strong> in the valley, <strong>3–5</strong> elsewhere</p>
      <p><strong>Cash</strong> on delivery or at pickup</p>
      <p><strong>14 days</strong> to return unused pieces</p>
    </aside>
    <div class="prose">
      <h2>Delivery fees</h2>
      <ul>
        <li>Kathmandu, Lalitpur and Bhaktapur: <?= e(money(setting('shipping_fee_valley', 150))) ?>, usually within 1–2 working days.</li>
        <li>Everywhere else in Nepal: <?= e(money(setting('shipping_fee_outside', 350))) ?>, usually 3–5 working days.</li>
        <li>Orders over <?= e(money(setting('free_shipping_threshold', 10000))) ?> (after any discount) are delivered free.</li>
        <?php if (setting('pickup_enabled') === '1'): ?><li>Collecting from the studio is always free: <?= e(setting('pickup_address')) ?>.</li><?php endif; ?>
      </ul>
      <h2>Paying</h2>
      <p>We only take payment in person: cash to the delivery driver, or cash at the studio when you collect. Prices exclude <?= e(setting('tax_label', 'VAT')) ?> at <?= e(setting('tax_rate', 13)) ?>%, which is added at checkout and shown on your invoice.</p>
      <h2>Changing or cancelling</h2>
      <p>You can cancel an order yourself from your account while it's still marked Pending. After that, call us on <?= e(setting('support_phone')) ?> and we'll do our best.</p>
      <h2>Returns</h2>
      <p>Return anything unused, in its original packaging, within 14 days of delivery. We'll collect it inside the valley; elsewhere, send it back with any courier and we'll refund the postage up to <?= e(money(setting('shipping_fee_outside', 350))) ?>. Refunds are made in cash or by bank transfer within five working days of the item reaching us.</p>
      <p>Made-to-order furniture, candles that have been lit and opened oils or soaps can't be returned unless they arrive damaged.</p>
      <h2>Something arrived broken</h2>
      <p>Send a photo to <?= e(setting('support_email')) ?> within 48 hours and we'll replace it or refund it, whichever you prefer.</p>
    </div>
  </div>
</div>
