<?php /** Expects $summary; optional $final (true at checkout: shipping is known). */ ?>
<dl class="totals" data-totals>
  <div><dt>Subtotal</dt><dd><?= e(money($summary['subtotal'])) ?></dd></div>
  <?php if ($summary['discount'] > 0): ?>
    <div class="is-discount"><dt>Discount (<?= e($summary['coupon']['code']) ?>)</dt><dd>−<?= e(money($summary['discount'])) ?></dd></div>
  <?php endif; ?>
  <div><dt>Delivery</dt><dd data-total-shipping><?= $summary['shipping'] === null ? 'From ' . e(money(setting('shipping_fee_valley', 0))) : ($summary['shipping'] > 0 ? e(money($summary['shipping'])) : 'Free') ?></dd></div>
  <div><dt><?= e(setting('tax_label', 'VAT')) ?> (<?= e(rtrim(rtrim(number_format($summary['tax_rate'], 2), '0'), '.')) ?>%)</dt><dd><?= e(money($summary['tax'])) ?></dd></div>
  <div class="totals__grand"><dt>Total</dt><dd data-total-grand><?= e(money($summary['total'])) ?></dd></div>
</dl>
