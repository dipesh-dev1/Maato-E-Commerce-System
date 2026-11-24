<?php
/** Expects $p with price_min, price_max, regular_min, on_sale. */
$from = (float) $p['price_max'] > (float) $p['price_min'];
?>
<span class="price-line">
  <?php if ($from): ?><span class="price-line__from">From</span><?php endif; ?>
  <span class="price-line__now<?= $p['on_sale'] ? ' is-sale' : '' ?>"><?= e(money($p['price_min'])) ?></span>
  <?php if ($p['on_sale'] && (float) $p['regular_min'] > (float) $p['price_min']): ?>
    <s><span class="visually-hidden">was </span><?= e(money($p['regular_min'])) ?></s>
  <?php endif; ?>
</span>
