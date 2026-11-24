<?php
use App\Models\OrderStatus;

$reached = array_column($history, 'created_at', 'status');
$currentIndex = array_search($order['status'], OrderStatus::FLOW, true);
?>
<?php partial('partials/account-shell-start', ['heading' => 'Order ' . $order['order_number']]); ?>

<?php if ($justPlaced): ?>
  <div class="notice notice--success"><?= icon('check-circle') ?><span>Thank you, your order is in. We'll call <?= e($order['ship_phone']) ?> to confirm, usually within a few hours. Keep <?= e(money($order['grand_total'])) ?> in cash ready for <?= $order['payment_method'] === 'pay_on_pickup' ? 'pickup' : 'delivery' ?>.</span></div>
<?php endif; ?>

<section class="panel" aria-labelledby="status-title">
  <div class="panel__head">
    <h2 id="status-title">Status</h2>
    <?php partial('partials/status-badge', ['status' => $order['status']]); ?>
  </div>
  <?php if ($order['status'] === 'cancelled'): ?>
    <p class="notice"><?= icon('info') ?><span>This order was cancelled on <?= e(format_date($order['cancelled_at'], 'j M Y, g:i a')) ?>. Nothing is owed.</span></p>
  <?php else: ?>
    <ol class="timeline">
      <?php foreach (OrderStatus::FLOW as $i => $step): ?>
        <li class="<?= $i < $currentIndex ? 'is-done' : ($i === $currentIndex ? 'is-current' : '') ?>">
          <strong><?= e(OrderStatus::label($step)) ?></strong>
          <?= isset($reached[$step]) ? e(format_date($reached[$step], 'j M, g:i a')) : '' ?>
        </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
  <?php if ($order['status'] === 'pending'): ?>
    <form method="post" action="<?= e(route('account.order.cancel', ['number' => $order['order_number']])) ?>" data-confirm="Cancel order <?= e($order['order_number']) ?>? The items go back on the shelf and nothing is owed.">
      <?= csrf_field() ?>
      <button class="btn btn--ghost btn--sm" type="submit">Cancel this order</button>
      <span class="hint">You can cancel until we confirm it.</span>
    </form>
  <?php endif; ?>
</section>

<section class="panel" aria-labelledby="items-title">
  <div class="panel__head"><h2 id="items-title">Items</h2><span class="small muted">Placed <?= e(format_date($order['created_at'], 'j M Y, g:i a')) ?></span></div>
  <table class="items-table">
    <thead><tr><th scope="col">Piece</th><th scope="col" class="num">Price</th><th scope="col" class="num">Qty</th><th scope="col" class="num">Total</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td>
            <div class="items-table__product">
              <img src="<?= e(media($item['image_path'])) ?>" alt="" width="52" height="65" loading="lazy">
              <span>
                <?php if ($item['product_slug']): ?><a href="<?= e(route('product', ['slug' => $item['product_slug']])) ?>"><?= e($item['product_name']) ?></a><?php else: ?><?= e($item['product_name']) ?><?php endif; ?>
                <?php if ($item['variant_label']): ?><br><span class="small muted"><?= e($item['variant_label']) ?></span><?php endif; ?>
              </span>
            </div>
          </td>
          <td class="num"><?= e(money($item['unit_price'])) ?></td>
          <td class="num"><?= (int) $item['quantity'] ?></td>
          <td class="num"><?= e(money($item['line_total'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="detail-grid section--tight">
    <div>
      <h3><?= $order['payment_method'] === 'pay_on_pickup' ? 'Collect from' : 'Delivering to' ?></h3>
      <address>
        <?= e($order['ship_recipient']) ?><br><?= e($order['ship_line1']) ?><br>
        <?php if ($order['ship_line2']): ?><?= e($order['ship_line2']) ?><br><?php endif; ?>
        <?php if ($order['payment_method'] !== 'pay_on_pickup'): ?><?= e($order['ship_city']) ?>, <?= e($order['ship_district']) ?><br><?= e($order['ship_province']) ?> <?= e((string) $order['ship_postal_code']) ?><br><?php endif; ?>
        <?= e($order['ship_phone']) ?>
      </address>
    </div>
    <div>
      <h3>Payment</h3>
      <p class="small"><?= e(OrderStatus::PAYMENT_LABELS[$order['payment_method']]) ?></p>
      <?php if ($order['note']): ?><h3 class="section--tight">Your note</h3><p class="small"><?= e($order['note']) ?></p><?php endif; ?>
    </div>
    <div>
      <h3>Summary</h3>
      <dl class="totals">
        <div><dt>Subtotal</dt><dd><?= e(money($order['subtotal'])) ?></dd></div>
        <?php if ((float) $order['discount_total'] > 0): ?><div class="is-discount"><dt>Discount (<?= e($order['coupon_code']) ?>)</dt><dd>−<?= e(money($order['discount_total'])) ?></dd></div><?php endif; ?>
        <div><dt>Delivery</dt><dd><?= (float) $order['shipping_total'] > 0 ? e(money($order['shipping_total'])) : 'Free' ?></dd></div>
        <div><dt><?= e(setting('tax_label', 'VAT')) ?> (<?= e(rtrim(rtrim($order['tax_rate'], '0'), '.')) ?>%)</dt><dd><?= e(money($order['tax_total'])) ?></dd></div>
        <div class="totals__grand"><dt>Total</dt><dd><?= e(money($order['grand_total'])) ?></dd></div>
      </dl>
    </div>
  </div>
</section>

<?php if (count($history) > 1 || $order['status'] === 'cancelled'): ?>
<section class="panel" aria-labelledby="history-title">
  <div class="panel__head"><h2 id="history-title">History</h2></div>
  <ol class="history">
    <?php foreach (array_reverse($history) as $h): ?>
      <li><time datetime="<?= e($h['created_at']) ?>"><?= e(format_date($h['created_at'], 'j M Y, g:i a')) ?></time><span><strong><?= e(OrderStatus::label($h['status'])) ?></strong><?= $h['note'] ? '. ' . e($h['note']) : '' ?></span></li>
    <?php endforeach; ?>
  </ol>
</section>
<?php endif; ?>

<?php partial('partials/account-shell-end'); ?>
