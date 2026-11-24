<?php
/**
 * Variant picker + add to bag. Works without JavaScript: the size/colour radios are resolved to a
 * variant on the server. With JavaScript the price, stock and SKU update live as options change.
 * Expects $product, $options, $wishlist; optional $idPrefix to keep ids unique inside quick view.
 */
$idPrefix ??= 'pdp';
$default = $options['default'];
$saved = in_array((int) $product['id'], $wishlist ?? [], true);
$stock = $default['stock'] ?? 0;
$low = (int) setting('low_stock_threshold', 5);
?>
<form class="stack stack--loose" method="post" action="<?= e(route('cart.add')) ?>" data-purchase-form
      data-variants="<?= e(json_encode($options['variants'], JSON_UNESCAPED_UNICODE)) ?>" data-low-stock="<?= $low ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

  <div class="pdp__price" data-price aria-live="polite">
    <span class="price-line">
      <span class="price-line__now num<?= $default['was'] ? ' is-sale' : '' ?>" data-price-now><?= e($default['price']) ?></span>
      <s data-price-was<?= $default['was'] ? '' : ' hidden' ?>><span class="visually-hidden">was </span><span><?= e((string) $default['was']) ?></span></s>
    </span>
    <span class="tag tag--sale" data-price-off<?= $default['off'] ? '' : ' hidden' ?>>−<span><?= (int) $default['off'] ?></span>%</span>
  </div>

  <?php if ($options['colors']): ?>
    <fieldset class="option" data-option="color">
      <legend><span>Colour <output data-option-label><?= e((string) $default['color']) ?></output></span></legend>
      <div class="option__choices">
        <?php foreach ($options['colors'] as $color => $hex): ?>
          <label class="option__choice">
            <input type="radio" name="color" value="<?= e($color) ?>"<?= $color === $default['color'] ? ' checked' : '' ?>>
            <span>
              <?php if ($hex): ?><svg class="swatch" viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="9.5" fill="<?= e($hex) ?>"/></svg><?php endif; ?>
              <?= e($color) ?>
            </span>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>
  <?php endif; ?>

  <?php if ($options['sizes']): ?>
    <fieldset class="option" data-option="size">
      <legend>
        <span>Size <output data-option-label><?= e((string) $default['size']) ?></output></span>
        <?php if (!empty($product['size_guide'])): ?>
          <button class="option__guide" type="button" data-open-dialog="size-guide" aria-haspopup="dialog"><?= icon('ruler', 'icon--sm') ?> Size guide</button>
        <?php endif; ?>
      </legend>
      <div class="option__choices">
        <?php foreach ($options['sizes'] as $size): ?>
          <label class="option__choice">
            <input type="radio" name="size" value="<?= e($size) ?>"<?= $size === $default['size'] ? ' checked' : '' ?>>
            <span><?= e($size) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>
  <?php elseif (!empty($product['size_guide'])): ?>
    <button class="option__guide" type="button" data-open-dialog="size-guide" aria-haspopup="dialog"><?= icon('ruler', 'icon--sm') ?> Dimensions &amp; size guide</button>
  <?php endif; ?>

  <div class="stack stack--tight">
    <p class="stock-note<?= $stock <= 0 ? ' is-out' : ($stock <= $low ? ' is-low' : '') ?>" data-stock-note>
      <?php if ($stock <= 0): ?>Sold out in this option. Try another, or check back soon.
      <?php elseif ($stock <= $low): ?>Only <?= (int) $stock ?> left. Ships in 1–2 days.
      <?php else: ?>In stock. Ships in 1–2 days.<?php endif; ?>
    </p>
    <div class="buy-row">
      <div class="qty" data-qty>
        <button type="button" data-qty-step="-1" aria-label="Decrease quantity"><?= icon('minus', 'icon--sm') ?></button>
        <label class="visually-hidden" for="<?= e($idPrefix) ?>-qty">Quantity</label>
        <input id="<?= e($idPrefix) ?>-qty" type="number" name="quantity" value="1" min="1" max="10" inputmode="numeric">
        <button type="button" data-qty-step="1" aria-label="Increase quantity"><?= icon('plus', 'icon--sm') ?></button>
      </div>
      <button class="btn btn--accent btn--lg" type="submit" data-add-button<?= $stock <= 0 ? ' disabled' : '' ?>>
        <?= $stock <= 0 ? 'Sold out' : 'Add to bag' ?>
      </button>
      <button class="wish-btn" type="submit" formaction="<?= e(route('wishlist.toggle', ['product' => $product['id']])) ?>" data-wishlist-button
              aria-pressed="<?= $saved ? 'true' : 'false' ?>" aria-label="Save for later">
        <?= icon('heart', 'icon-off') ?><?= icon('heart-fill', 'icon-on') ?>
      </button>
    </div>
    <p class="sku">SKU <span data-sku><?= e((string) ($default['sku'] ?? '')) ?></span></p>
  </div>
</form>
