<?php /** Cart page body; re-rendered after every change. Expects $lines, $summary. */ ?>
<?php if (!$lines): ?>
  <?php partial('partials/empty', [
      'icon' => 'bag', 'heading' => 'Nothing in your bag yet',
      'body' => "When you find something you like, add it here. We'll hold your bag while you keep browsing, and it'll still be here if you sign in later.",
      'actions' => [['Shop the edit', route('shop'), true], ['See saved pieces', route('account.wishlist')]],
  ]); ?>
<?php else: ?>
  <div class="cart-layout">
    <div>
      <?php partial('store/partials/ship-meter', ['summary' => $summary]); ?>
      <ul class="cart-drawer__lines" aria-label="Items in your bag">
        <?php foreach ($lines as $line): ?><?php partial('store/partials/cart-line', ['line' => $line]); ?><?php endforeach; ?>
      </ul>
    </div>
    <aside class="summary-card" aria-labelledby="summary-title">
      <h2 class="h4" id="summary-title">Summary</h2>
      <?php if ($summary['coupon']): ?>
        <div class="coupon-applied">
          <span><?= icon('tag', 'icon--sm') ?> <strong><?= e($summary['coupon']['code']) ?></strong> applied</span>
          <form method="post" action="<?= e(route('cart.coupon.remove')) ?>" data-cart-coupon>
            <?= csrf_field() ?><?= method_field('DELETE') ?>
            <button class="remove-btn" type="submit">Remove</button>
          </form>
        </div>
      <?php else: ?>
        <form class="coupon-form" method="post" action="<?= e(route('cart.coupon')) ?>" data-cart-coupon>
          <?= csrf_field() ?>
          <label class="visually-hidden" for="coupon-code">Discount code</label>
          <input class="input" id="coupon-code" name="code" placeholder="Discount code" maxlength="30" autocomplete="off">
          <button class="btn btn--ghost btn--sm" type="submit">Apply</button>
        </form>
        <?php if ($summary['coupon_error']): ?><p class="field-error"><?= e($summary['coupon_error']) ?></p><?php endif; ?>
      <?php endif; ?>
      <?php partial('store/partials/totals', ['summary' => $summary]); ?>
      <?php if ($summary['has_problems']): ?>
        <p class="notice notice--warning"><?= icon('alert') ?><span>Please fix the items marked in red before checking out.</span></p>
      <?php endif; ?>
      <a class="btn btn--accent btn--lg btn--block" href="<?= e(route('checkout')) ?>"<?= $summary['has_problems'] ? ' aria-disabled="true"' : '' ?>>Continue to checkout</a>
      <p class="secure-note"><?= icon('cash', 'icon--sm') ?> Cash on delivery or pay on pickup</p>
    </aside>
  </div>
<?php endif; ?>
