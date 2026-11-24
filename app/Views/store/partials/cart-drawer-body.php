<?php /** Drawer contents; re-rendered after every bag change. Expects $lines, $summary. */ ?>
<?php if (!$lines): ?>
  <?php partial('partials/empty', [
      'icon' => 'bag', 'heading' => 'Your bag is empty',
      'body' => 'A good place to start: the Chiya mug, which is what we drink our own tea from.',
      'actions' => [['Shop the edit', route('shop'), true]],
  ]); ?>
<?php else: ?>
  <?php partial('store/partials/ship-meter', ['summary' => $summary]); ?>
  <ul class="cart-drawer__lines">
    <?php foreach ($lines as $line): ?><?php partial('store/partials/cart-line', ['line' => $line, 'size' => 'drawer']); ?><?php endforeach; ?>
  </ul>
  <div class="cart-drawer__foot">
    <dl class="totals"><div class="totals__grand"><dt>Subtotal</dt><dd><?= e(money($summary['subtotal'])) ?></dd></div></dl>
    <p class="small muted">Delivery and <?= e(setting('tax_label', 'VAT')) ?> are added at checkout. You pay when your order arrives.</p>
    <?php if ($summary['has_problems']): ?>
      <p class="notice notice--warning"><?= icon('alert') ?><span>Something in your bag has changed. Check the items marked in red.</span></p>
    <?php endif; ?>
    <a class="btn btn--accent btn--lg btn--block" href="<?= e(route('checkout')) ?>"<?= $summary['has_problems'] ? ' aria-disabled="true"' : '' ?>>Checkout</a>
    <a class="btn btn--ghost btn--block" href="<?= e(route('cart')) ?>">View bag &amp; add a code</a>
  </div>
<?php endif; ?>
