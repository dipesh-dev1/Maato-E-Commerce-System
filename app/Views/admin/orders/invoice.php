<?php use App\Models\OrderStatus; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice <?= e($order['order_number']) ?> · <?= e(setting('store_name', 'Maato')) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..500&amp;family=Instrument+Sans:wght@400..600&amp;display=swap">
<link rel="stylesheet" href="<?= e(asset('css/print.css')) ?>">
<script type="module" src="<?= e(asset('js/admin/invoice.js')) ?>"></script>
</head>
<body>
<div class="toolbar no-print"><button type="button" data-print>Print or save as PDF</button></div>
<main class="sheet">
  <header class="head">
    <div>
      <p class="brand"><?= e(mb_strtolower(setting('store_name', 'Maato'))) ?><span>.</span></p>
      <p class="muted"><?= e(setting('pickup_address')) ?><br><?= e(setting('support_phone')) ?> · <?= e(setting('support_email')) ?></p>
    </div>
    <div class="right">
      <h1>Tax invoice</h1>
      <p><strong><?= e($order['order_number']) ?></strong><br><?= e(format_date($order['created_at'], 'j F Y')) ?><br><?= e(OrderStatus::PAYMENT_LABELS[$order['payment_method']]) ?></p>
    </div>
  </header>
  <section class="parties">
    <div><h2>Billed to</h2><p><?= e($order['customer_name']) ?><br><?= e($order['customer_email']) ?><br><?= e((string) $order['customer_phone']) ?></p></div>
    <div><h2><?= $order['payment_method'] === 'pay_on_pickup' ? 'Collection' : 'Delivered to' ?></h2>
      <p><?= e($order['ship_recipient']) ?><br><?= e($order['ship_line1']) ?><?= $order['ship_line2'] ? '<br>' . e($order['ship_line2']) : '' ?>
      <?php if ($order['payment_method'] !== 'pay_on_pickup'): ?><br><?= e($order['ship_city']) ?>, <?= e($order['ship_district']) ?>, <?= e($order['ship_province']) ?><?php endif; ?><br><?= e($order['ship_phone']) ?></p>
    </div>
  </section>
  <table class="lines">
    <thead><tr><th>Item</th><th>SKU</th><th class="num">Unit</th><th class="num">Qty</th><th class="num">Amount</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr><td><?= e($item['product_name']) ?><?php if ($item['variant_label']): ?><br><small><?= e($item['variant_label']) ?></small><?php endif; ?></td><td><?= e($item['sku']) ?></td><td class="num"><?= e(money($item['unit_price'])) ?></td><td class="num"><?= (int) $item['quantity'] ?></td><td class="num"><?= e(money($item['line_total'])) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <table class="sum">
    <tr><th>Subtotal</th><td><?= e(money($order['subtotal'])) ?></td></tr>
    <?php if ((float) $order['discount_total'] > 0): ?><tr><th>Discount (<?= e($order['coupon_code']) ?>)</th><td>−<?= e(money($order['discount_total'])) ?></td></tr><?php endif; ?>
    <tr><th>Delivery</th><td><?= e(money($order['shipping_total'])) ?></td></tr>
    <tr><th><?= e(setting('tax_label', 'VAT')) ?> <?= e(rtrim(rtrim($order['tax_rate'], '0'), '.')) ?>%</th><td><?= e(money($order['tax_total'])) ?></td></tr>
    <tr class="grand"><th>Total (<?= e($order['currency']) ?>)</th><td><?= e(money($order['grand_total'])) ?></td></tr>
  </table>
  <footer class="foot">
    <p><?= $order['status'] === 'cancelled' ? 'This order was cancelled. No payment is due.' : 'Payment is collected in cash ' . ($order['payment_method'] === 'pay_on_pickup' ? 'at the studio.' : 'on delivery.') ?></p>
    <p>Thank you for buying from small workshops. Returns within 14 days of delivery, unused and in original packaging.</p>
  </footer>
</main>
</body>
</html>
