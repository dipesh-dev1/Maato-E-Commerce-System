<?php use App\Models\OrderStatus; ?>
<div class="page-head">
  <div>
    <h1 class="h1">Order <?= e($order['order_number']) ?> <?php partial('partials/status-badge', ['status' => $order['status']]); ?></h1>
    <p>Placed <?= e(format_date($order['created_at'], 'l j F Y, g:i a')) ?> · <?= e(OrderStatus::PAYMENT_LABELS[$order['payment_method']]) ?></p>
  </div>
  <div class="cluster">
    <a class="btn btn--ghost" href="<?= e(route('admin.orders.invoice', ['id' => $order['id']])) ?>" target="_blank" rel="noopener"><?= icon('printer', 'icon--sm') ?> Invoice</a>
    <?php if ($next): ?><button class="btn" type="button" data-open-dialog="status-form"><?= icon('refresh', 'icon--sm') ?> Change status</button><?php endif; ?>
  </div>
</div>

<div class="grid-2">
  <div class="stack-gap">
    <section class="a-card" aria-labelledby="items-title">
      <div class="a-card__head"><h2 id="items-title">Items</h2><span class="muted"><?= e(plural((int) array_sum(array_column($items, 'quantity')), 'piece')) ?></span></div>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th scope="col">Product</th><th scope="col">SKU</th><th scope="col" class="num">Unit price</th><th scope="col" class="num">Qty</th><th scope="col" class="num">Total</th></tr></thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <div class="cell-product">
                    <img src="<?= e(media($item['image_path'])) ?>" alt="" loading="lazy">
                    <span><?php if ($item['product_id'] && can('products.manage')): ?><a href="<?= e(route('admin.products.edit', ['id' => $item['product_id']])) ?>"><?= e($item['product_name']) ?></a><?php else: ?><?= e($item['product_name']) ?><?php endif; ?>
                    <?php if ($item['variant_label']): ?><span class="cell-sub"><?= e($item['variant_label']) ?></span><?php endif; ?></span>
                  </div>
                </td>
                <td class="muted"><?= e($item['sku']) ?></td>
                <td class="num"><?= e(money($item['unit_price'])) ?></td>
                <td class="num"><?= (int) $item['quantity'] ?></td>
                <td class="num"><?= e(money($item['line_total'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="a-card__body">
        <dl class="totals">
          <div><dt>Subtotal</dt><dd><?= e(money($order['subtotal'])) ?></dd></div>
          <?php if ((float) $order['discount_total'] > 0): ?><div class="is-discount"><dt>Discount (<?= e($order['coupon_code']) ?>)</dt><dd>−<?= e(money($order['discount_total'])) ?></dd></div><?php endif; ?>
          <div><dt>Delivery</dt><dd><?= (float) $order['shipping_total'] > 0 ? e(money($order['shipping_total'])) : 'Free' ?></dd></div>
          <div><dt><?= e(setting('tax_label', 'VAT')) ?> (<?= e(rtrim(rtrim($order['tax_rate'], '0'), '.')) ?>%)</dt><dd><?= e(money($order['tax_total'])) ?></dd></div>
          <div class="totals__grand"><dt>To collect</dt><dd><?= e(money($order['grand_total'])) ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="a-card" aria-labelledby="history-title">
      <div class="a-card__head"><h2 id="history-title">Status history</h2></div>
      <div class="a-card__body">
        <ol class="timeline-v">
          <?php foreach (array_reverse($history) as $h): ?>
            <li>
              <?php partial('partials/status-badge', ['status' => $h['status']]); ?>
              <?php if ($h['note']): ?> <?= e($h['note']) ?><?php endif; ?>
              <small><?= e(format_date($h['created_at'], 'j M Y, g:i a')) ?> · <?= e($h['changed_by_name'] ?? 'System') ?></small>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </section>
  </div>

  <div class="stack-gap">
    <section class="a-card" aria-labelledby="customer-title">
      <div class="a-card__head"><h2 id="customer-title">Customer</h2><?php if (can('users.manage')): ?><a class="link-btn" href="<?= e(route('admin.users.show', ['id' => $order['user_id']])) ?>">Profile</a><?php endif; ?></div>
      <div class="a-card__body">
        <dl class="dl">
          <dt>Name</dt><dd><?= e($order['customer_name']) ?></dd>
          <dt>Email</dt><dd><a href="mailto:<?= e($order['customer_email']) ?>"><?= e($order['customer_email']) ?></a></dd>
          <dt>Phone</dt><dd><a href="tel:<?= e($order['ship_phone']) ?>"><?= e($order['ship_phone']) ?></a></dd>
        </dl>
      </div>
    </section>
    <section class="a-card" aria-labelledby="ship-title">
      <div class="a-card__head"><h2 id="ship-title"><?= $order['payment_method'] === 'pay_on_pickup' ? 'Collecting from studio' : 'Deliver to' ?></h2></div>
      <div class="a-card__body">
        <address class="small">
          <?= e($order['ship_recipient']) ?><br><?= e($order['ship_line1']) ?><br>
          <?php if ($order['ship_line2']): ?><?= e($order['ship_line2']) ?><br><?php endif; ?>
          <?php if ($order['payment_method'] !== 'pay_on_pickup'): ?><?= e($order['ship_city']) ?>, <?= e($order['ship_district']) ?><br><?= e($order['ship_province']) ?> <?= e((string) $order['ship_postal_code']) ?><?php endif; ?>
        </address>
        <?php if ($order['note']): ?><p class="notice section--tight"><?= icon('message') ?><span><strong>Customer note:</strong> <?= e($order['note']) ?></span></p><?php endif; ?>
      </div>
    </section>
  </div>
</div>

<?php if ($next): ?>
<dialog class="drawer slideover" id="status-form" aria-labelledby="status-form-title">
  <form method="post" action="<?= e(route('admin.orders.status', ['id' => $order['id']])) ?>" data-record-form data-keep-action>
    <div class="dialog__head">
      <h2 class="dialog__title" id="status-form-title">Change status</h2>
      <button class="icon-btn" type="button" data-close-dialog aria-label="Close"><?= icon('close') ?></button>
    </div>
    <div class="dialog__body">
      <?= csrf_field() ?>
      <p class="muted">Currently <strong><?= e(OrderStatus::label($order['status'])) ?></strong>. The customer sees each change on their order page.</p>
      <fieldset class="field section--tight">
        <legend class="label">Move to</legend>
        <?php foreach ($next as $i => $s): ?>
          <label class="check"><input type="radio" name="status" value="<?= e($s) ?>"<?= $i === 0 ? ' checked' : '' ?>> <?= e(OrderStatus::label($s)) ?><?= $s === 'cancelled' ? ' (returns stock to the shelf)' : '' ?></label>
        <?php endforeach; ?>
        <?php partial('admin/partials/field-error', ['name' => 'status']); ?>
      </fieldset>
      <div class="field">
        <label class="label" for="status-note">Note <span class="optional">(shown to the customer)</span></label>
        <textarea class="textarea" id="status-note" name="note" rows="3" maxlength="500" placeholder="Confirmed by phone. Delivery Thursday afternoon."></textarea>
        <?php partial('admin/partials/field-error', ['name' => 'note']); ?>
      </div>
    </div>
    <?php partial('admin/partials/slideover-foot'); ?>
  </form>
</dialog>
<?php endif; ?>
