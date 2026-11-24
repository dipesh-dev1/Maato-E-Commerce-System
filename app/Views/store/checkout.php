<?php
/** @var array $lines @var array $addresses @var array $summary @var array $quotes */
?>
<div class="container">
  <form class="checkout-layout" method="post" action="<?= e(route('checkout.place')) ?>" data-checkout data-quotes="<?= e(json_encode($quotes)) ?>">
    <?= csrf_field() ?>
    <div>
      <h1 class="h2">Checkout</h1>
      <p class="muted">Signed in as <?= e(auth_user()['email']) ?>. Nothing is charged online.</p>

      <section class="checkout-step" aria-labelledby="step-method">
        <h2 class="checkout-step__title" id="step-method"><span class="checkout-step__num">1</span> How would you like it?</h2>
        <div class="choice-cards">
          <label class="choice-card">
            <span class="check"><input type="radio" name="payment_method" value="cash_on_delivery"<?= $method !== 'pay_on_pickup' ? ' checked' : '' ?> data-method></span>
            <span><strong>Deliver it · cash on delivery</strong><span>Pay the driver in cash when it arrives. Valley 1–2 days, rest of Nepal 3–5.</span></span>
          </label>
          <?php if ($pickupEnabled): ?>
            <label class="choice-card">
              <span class="check"><input type="radio" name="payment_method" value="pay_on_pickup"<?= $method === 'pay_on_pickup' ? ' checked' : '' ?> data-method></span>
              <span><strong>Collect it · pay on pickup</strong><span><?= e(setting('pickup_address')) ?>. We'll call when it's ready, usually the next day.</span></span>
            </label>
          <?php endif; ?>
        </div>
        <?= field_error('payment_method') ?>
      </section>

      <section class="checkout-step" aria-labelledby="step-address" data-delivery-only<?= $method === 'pay_on_pickup' ? ' hidden' : '' ?>>
        <h2 class="checkout-step__title" id="step-address"><span class="checkout-step__num">2</span> Where should we deliver?</h2>
        <?php if ($addresses): ?>
          <div class="choice-cards">
            <?php foreach ($addresses as $a): ?>
              <label class="choice-card">
                <span class="check"><input type="radio" name="address_id" value="<?= (int) $a['id'] ?>"<?= (int) $a['id'] === $selectedAddress ? ' checked' : '' ?> data-address></span>
                <span>
                  <strong><?= e($a['label']) ?><?= $a['is_default'] ? ' · Default' : '' ?></strong>
                  <address><?= e($a['recipient_name']) ?><br><?= e($a['line1']) ?><?= $a['line2'] ? '<br>' . e($a['line2']) : '' ?><br><?= e($a['city']) ?>, <?= e($a['district']) ?><br><?= e($a['phone']) ?></address>
                </span>
              </label>
            <?php endforeach; ?>
            <a class="choice-card choice-card--add" href="<?= e(url('/account/addresses', ['return' => 'checkout'])) ?>#new-address"><?= icon('plus') ?> Add a new address</a>
          </div>
        <?php else: ?>
          <div class="notice"><?= icon('pin') ?><span>You haven't saved an address yet. <a href="<?= e(url('/account/addresses', ['return' => 'checkout'])) ?>#new-address">Add one</a> and we'll bring you straight back here.</span></div>
        <?php endif; ?>
      </section>

      <section class="checkout-step" aria-labelledby="step-contact" data-pickup-only<?= $method === 'pay_on_pickup' ? '' : ' hidden' ?>>
        <h2 class="checkout-step__title" id="step-contact"><span class="checkout-step__num">2</span> How can we reach you?</h2>
        <div class="field">
          <label class="label" for="contact-phone">Phone number</label>
          <input class="input" id="contact-phone" name="contact_phone" type="tel" autocomplete="tel" value="<?= e(old('contact_phone', auth_user()['phone'] ?? '')) ?>"<?= aria_invalid('contact_phone') ?>>
          <p class="hint">We'll call this number when your order is ready to collect.</p>
          <?= field_error('contact_phone') ?>
        </div>
      </section>

      <section class="checkout-step" aria-labelledby="step-note">
        <h2 class="checkout-step__title" id="step-note"><span class="checkout-step__num">3</span> Anything we should know? <span class="small muted">Optional</span></h2>
        <label class="visually-hidden" for="order-note">Order note</label>
        <textarea class="textarea" id="order-note" name="note" maxlength="500" placeholder="Gate code, best time to call, gift wrapping…"><?= e(old('note')) ?></textarea>
        <?= field_error('note') ?>
      </section>
    </div>

    <aside class="summary-card" aria-labelledby="checkout-summary">
      <h2 class="h4" id="checkout-summary">Your order</h2>
      <ul class="order-lines">
        <?php foreach ($lines as $line): ?>
          <li>
            <img src="<?= e(media($line['image'])) ?>" alt="" width="56" height="70">
            <span><?= e($line['name']) ?><?php if ($line['label']): ?><br><span class="qty-badge"><?= e($line['label']) ?></span><?php endif; ?><br><span class="qty-badge">Qty <?= (int) $line['quantity'] ?></span></span>
            <span class="num"><?= e(money($line['line_total'])) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php partial('store/partials/totals', ['summary' => $summary]); ?>
      <?php if ($summary['has_problems']): ?>
        <p class="notice notice--warning"><?= icon('alert') ?><span>Some items have changed. <a href="<?= e(route('cart')) ?>">Review your bag</a> first.</span></p>
      <?php endif; ?>
      <button class="btn btn--accent btn--lg btn--block" type="submit" data-place-order<?= $summary['has_problems'] ? ' disabled' : '' ?>>Place order · <span data-total-button><?= e(money($summary['total'])) ?></span></button>
      <p class="secure-note"><?= icon('lock', 'icon--sm') ?> You'll pay in cash. No card details, ever.</p>
    </aside>
  </form>
</div>
