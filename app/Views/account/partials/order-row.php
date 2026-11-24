<a class="order-row" href="<?= e(route('account.order', ['number' => $o['order_number']])) ?>">
  <img src="<?= e(media($o['image'])) ?>" alt="" width="64" height="80" loading="lazy">
  <span>
    <span class="order-row__num"><?= e($o['order_number']) ?></span>
    <span class="order-row__meta"><br><?= e(format_date($o['created_at'])) ?> · <?= e(plural((int) $o['item_count'], 'item')) ?></span>
  </span>
  <?php partial('partials/status-badge', ['status' => $o['status']]); ?>
  <span class="num"><?= e(money($o['grand_total'])) ?></span>
</a>
