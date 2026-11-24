<?php $s = static fn (string $k, $d = '') => old($k, $settings[$k] ?? $d); ?>
<div class="page-head">
  <div><h1 class="h1">Store settings</h1><p>Prices, delivery and tax apply to new carts immediately. Existing orders keep the values they were placed with.</p></div>
  <button class="btn" type="submit" form="settings-form">Save settings</button>
</div>
<?php if (!$gd): ?>
  <p class="notice notice--warning"><?= icon('alert') ?><span>The PHP GD extension is off, so uploaded images are stored as-is without resized thumbnails. Enable <code>extension=gd</code> in php.ini and restart Apache.</span></p>
<?php endif; ?>
<form id="settings-form" class="grid-halves" method="post" action="<?= e(route('admin.settings.update')) ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="stack-gap">
    <section class="a-card a-card__body">
      <div class="form-section">
        <h3>Brand</h3>
        <div class="field"><label class="label" for="store_name">Store name</label><input class="input" id="store_name" name="store_name" required maxlength="60" value="<?= e($s('store_name')) ?>"<?= aria_invalid('store_name') ?>><?= field_error('store_name') ?></div>
        <div class="field"><label class="label" for="store_tagline">Tagline</label><input class="input" id="store_tagline" name="store_tagline" maxlength="160" value="<?= e($s('store_tagline')) ?>"><?= field_error('store_tagline') ?></div>
        <div class="field">
          <label class="label" for="logo">Logo <span class="optional">(optional; the wordmark is used otherwise)</span></label>
          <?php if (!empty($settings['logo_path'])): ?>
            <div class="cluster"><img src="<?= e(media($settings['logo_path'])) ?>" alt="Current logo" height="40"><label class="check"><input type="checkbox" name="remove_logo" value="1"> Remove logo</label></div>
          <?php endif; ?>
          <input class="file-input" id="logo" type="file" name="logo" accept="image/png,image/webp,image/jpeg"<?= aria_invalid('logo') ?>>
          <?= field_error('logo') ?>
        </div>
      </div>
      <div class="form-section">
        <h3>Contact</h3>
        <div class="field-row">
          <div class="field"><label class="label" for="support_email">Support email</label><input class="input" id="support_email" type="email" name="support_email" required value="<?= e($s('support_email')) ?>"<?= aria_invalid('support_email') ?>><?= field_error('support_email') ?></div>
          <div class="field"><label class="label" for="support_phone">Support phone</label><input class="input" id="support_phone" name="support_phone" required maxlength="30" value="<?= e($s('support_phone')) ?>"><?= field_error('support_phone') ?></div>
        </div>
      </div>
    </section>
    <section class="a-card a-card__body">
      <div class="form-section">
        <h3>Inventory</h3>
        <div class="field"><label class="label" for="low_stock_threshold">Low-stock warning at</label><input class="input" id="low_stock_threshold" type="number" min="0" max="1000" name="low_stock_threshold" required value="<?= e($s('low_stock_threshold', 5)) ?>"><p class="hint">Variants at or below this number show “only N left” in the shop and appear on the dashboard.</p><?= field_error('low_stock_threshold') ?></div>
      </div>
    </section>
  </div>
  <div class="stack-gap">
    <section class="a-card a-card__body">
      <div class="form-section">
        <h3>Money</h3>
        <div class="field-row">
          <div class="field"><label class="label" for="currency_code">Currency</label><select class="select" id="currency_code" name="currency_code"><?php foreach (['NPR', 'INR', 'USD'] as $c): ?><option<?= $s('currency_code') === $c ? ' selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select></div>
          <div class="field"><label class="label" for="currency_symbol">Symbol</label><input class="input" id="currency_symbol" name="currency_symbol" required maxlength="6" value="<?= e($s('currency_symbol')) ?>"><?= field_error('currency_symbol') ?></div>
        </div>
        <div class="field-row">
          <div class="field"><label class="label" for="tax_label">Tax name</label><input class="input" id="tax_label" name="tax_label" required maxlength="20" value="<?= e($s('tax_label', 'VAT')) ?>"><?= field_error('tax_label') ?></div>
          <div class="field"><label class="label" for="tax_rate">Tax rate (%)</label><input class="input" id="tax_rate" type="number" min="0" max="50" step="0.01" name="tax_rate" required value="<?= e($s('tax_rate')) ?>"><?= field_error('tax_rate') ?></div>
        </div>
        <p class="hint section--tight">Amounts use Nepali digit grouping, e.g. <?= e(money(125000)) ?>. Tax is added on top of product prices, after discounts.</p>
      </div>
      <div class="form-section">
        <h3>Delivery</h3>
        <div class="field-row">
          <div class="field"><label class="label" for="shipping_fee_valley">Inside the valley</label><input class="input" id="shipping_fee_valley" type="number" min="0" step="1" name="shipping_fee_valley" required value="<?= e($s('shipping_fee_valley')) ?>"><?= field_error('shipping_fee_valley') ?></div>
          <div class="field"><label class="label" for="shipping_fee_outside">Rest of Nepal</label><input class="input" id="shipping_fee_outside" type="number" min="0" step="1" name="shipping_fee_outside" required value="<?= e($s('shipping_fee_outside')) ?>"><?= field_error('shipping_fee_outside') ?></div>
        </div>
        <div class="field"><label class="label" for="free_shipping_threshold">Free delivery from</label><input class="input" id="free_shipping_threshold" type="number" min="0" step="1" name="free_shipping_threshold" value="<?= e($s('free_shipping_threshold')) ?>"><p class="hint">Bag value after discounts. 0 turns free delivery off.</p><?= field_error('free_shipping_threshold') ?></div>
        <div class="field"><label class="label" for="valley_districts">Districts charged the valley rate</label><input class="input" id="valley_districts" name="valley_districts" required value="<?= e($s('valley_districts')) ?>"><p class="hint">Comma-separated, matched against the district on each address.</p><?= field_error('valley_districts') ?></div>
      </div>
      <div class="form-section">
        <h3>Pickup</h3>
        <label class="switch"><input type="hidden" name="pickup_enabled" value="0"><input type="checkbox" name="pickup_enabled" value="1"<?= $s('pickup_enabled') === '1' ? ' checked' : '' ?>> Offer “pay on pickup” at checkout</label>
        <div class="field section--tight"><label class="label" for="pickup_address">Pickup address &amp; hours</label><input class="input" id="pickup_address" name="pickup_address" maxlength="255" value="<?= e($s('pickup_address')) ?>"<?= aria_invalid('pickup_address') ?>><?= field_error('pickup_address') ?></div>
      </div>
    </section>
  </div>
</form>
