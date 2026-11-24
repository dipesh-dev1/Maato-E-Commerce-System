<?php
use App\Models\OrderStatus;

$series = array_map(static fn ($p) => $p + ['label' => money($p['revenue'])], $series);
$change = $kpis['revenue_change'];
?>
<div class="page-head">
  <div>
    <h1 class="h1">Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 17 ? 'afternoon' : 'evening') ?>, <?= e(explode(' ', auth_user()['name'])[0]) ?></h1>
    <p><?= $kpis['orders_today'] ? e(plural($kpis['orders_today'], 'order')) . ' so far today.' : 'No orders yet today.' ?> <?= ($statusCounts['pending'] ?? 0) ? e(plural($statusCounts['pending'], 'order')) . ' waiting to be confirmed.' : 'Nothing waiting to be confirmed.' ?></p>
  </div>
  <?php if (can('orders.manage')): ?>
    <div class="cluster">
      <a class="btn btn--ghost" href="<?= e(url('/admin/orders', ['status' => 'pending'])) ?>">Confirm pending orders</a>
      <?php if (can('products.manage')): ?><a class="btn" href="<?= e(route('admin.products.create')) ?>"><?= icon('plus', 'icon--sm') ?> New product</a><?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<section class="kpis" aria-label="Key figures">
  <div class="a-card kpi">
    <span class="kpi__label">Revenue, last 30 days</span>
    <span class="kpi__value"><?= e(money($kpis['revenue_30'])) ?></span>
    <span class="kpi__meta"><?= $change === null ? 'No earlier period to compare' : ($change >= 0 ? '▲ ' : '▼ ') . e(number_format(abs($change), 1)) . '% vs the 30 days before' ?></span>
  </div>
  <div class="a-card kpi">
    <span class="kpi__label">Revenue today</span>
    <span class="kpi__value"><?= e(money($kpis['revenue_today'])) ?></span>
    <span class="kpi__meta">Excludes cancelled orders</span>
  </div>
  <div class="a-card kpi">
    <span class="kpi__label">Orders</span>
    <span class="kpi__value"><?= (int) $kpis['orders_today'] ?> <span class="small muted">today</span></span>
    <span class="kpi__meta"><?= (int) $kpis['orders_week'] ?> this week · <?= (int) $kpis['orders_month'] ?> this month</span>
  </div>
  <div class="a-card kpi">
    <span class="kpi__label">New customers, 30 days</span>
    <span class="kpi__value"><?= (int) $kpis['new_customers'] ?></span>
    <span class="kpi__meta"><?= (int) $kpis['pending_reviews'] ?> reviews to moderate</span>
  </div>
  <div class="a-card kpi<?= $kpis['low_stock'] ? ' kpi--alert' : '' ?>">
    <span class="kpi__label">Low or out of stock</span>
    <span class="kpi__value"><?= (int) $kpis['low_stock'] ?></span>
    <span class="kpi__meta"><?php if (can('products.manage')): ?><a href="<?= e(url('/admin/products', ['stock' => 'low'])) ?>">Variants at <?= (int) $threshold ?> or fewer →</a><?php else: ?>Variants at <?= (int) $threshold ?> or fewer<?php endif; ?></span>
  </div>
</section>

<div class="grid-2">
  <div class="stack-gap">
    <section class="a-card" aria-labelledby="sales-title">
      <div class="a-card__head">
        <div>
          <h2 id="sales-title">Daily revenue</h2>
          <p class="small muted" data-chart-summary><?= e(money(array_sum(array_column($series, 'revenue')))) ?> from <?= e(plural(array_sum(array_column($series, 'orders')), 'order')) ?></p>
        </div>
        <div class="segmented" role="group" aria-label="Date range">
          <?php foreach ([7, 30, 90] as $d): ?>
            <button type="button" data-chart-range="<?= $d ?>" aria-pressed="<?= $d === 30 ? 'true' : 'false' ?>"><?= $d ?> days</button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="a-card__body">
        <div class="chart" data-chart data-endpoint="<?= e(route('admin.sales')) ?>" data-series="<?= e(json_encode($series)) ?>" data-currency="<?= e(setting('currency_symbol', 'Rs.')) ?>">
          <svg role="img" aria-labelledby="sales-title" data-chart-svg></svg>
          <div class="chart__tip" data-chart-tip hidden></div>
        </div>
        <details class="small">
          <summary class="link-btn">Show as a table</summary>
          <div class="table-wrap">
            <table class="data-table" data-chart-table>
              <thead><tr><th scope="col">Date</th><th scope="col" class="num">Orders</th><th scope="col" class="num">Revenue</th></tr></thead>
              <tbody>
                <?php foreach (array_reverse($series) as $p): ?>
                  <tr><td><?= e(format_date($p['date'], 'D j M')) ?></td><td class="num"><?= (int) $p['orders'] ?></td><td class="num"><?= e($p['label']) ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </details>
      </div>
    </section>

    <section class="a-card" aria-labelledby="recent-title">
      <div class="a-card__head"><h2 id="recent-title">Latest orders</h2><?php if (can('orders.manage')): ?><a class="link-btn" href="<?= e(route('admin.orders')) ?>">All orders</a><?php endif; ?></div>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th scope="col">Order</th><th scope="col">Customer</th><th scope="col">Status</th><th scope="col">Placed</th><th scope="col" class="num">Total</th></tr></thead>
          <tbody>
            <?php foreach ($recent as $o): ?>
              <tr>
                <td><?php if (can('orders.manage')): ?><a href="<?= e(route('admin.orders.show', ['id' => $o['id']])) ?>"><?= e($o['order_number']) ?></a><?php else: ?><?= e($o['order_number']) ?><?php endif; ?></td>
                <td><?= e($o['customer_name']) ?></td>
                <td><?php partial('partials/status-badge', ['status' => $o['status']]); ?></td>
                <td><?= e(format_date($o['created_at'], 'j M, g:i a')) ?></td>
                <td class="num"><?= e(money($o['grand_total'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <div class="stack-gap">
    <section class="a-card" aria-labelledby="pipeline-title">
      <div class="a-card__head"><h2 id="pipeline-title">Order pipeline</h2></div>
      <div class="a-card__body">
        <dl class="dl">
          <?php foreach (OrderStatus::LABELS as $status => $label): ?>
            <dt><?php partial('partials/status-badge', ['status' => $status]); ?></dt>
            <dd class="num"><?php if (can('orders.manage')): ?><a href="<?= e(url('/admin/orders', ['status' => $status])) ?>"><?= (int) ($statusCounts[$status] ?? 0) ?></a><?php else: ?><?= (int) ($statusCounts[$status] ?? 0) ?><?php endif; ?></dd>
          <?php endforeach; ?>
        </dl>
      </div>
    </section>

    <section class="a-card" aria-labelledby="top-title">
      <div class="a-card__head"><h2 id="top-title">Best sellers, 30 days</h2></div>
      <?php if ($top): ?>
        <ol class="mini-list">
          <?php foreach ($top as $t): ?>
            <li>
              <img src="<?= e(media($t['image'])) ?>" alt="" loading="lazy">
              <span class="grow"><span><?= e($t['product_name']) ?></span><small><?= e(plural((int) $t['units'], 'unit')) ?> sold</small></span>
              <span class="num"><?= e(money($t['revenue'])) ?></span>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php else: ?><p class="a-card__body muted">No sales in the last 30 days.</p><?php endif; ?>
    </section>

    <section class="a-card" aria-labelledby="stock-title">
      <div class="a-card__head"><h2 id="stock-title">Low stock</h2></div>
      <?php if ($lowStock): ?>
        <ul class="mini-list">
          <?php foreach ($lowStock as $v): ?>
            <li>
              <span class="grow">
                <?php if (can('products.manage')): ?><a href="<?= e(route('admin.products.edit', ['id' => $v['product_id']])) ?>"><?= e($v['name']) ?></a><?php else: ?><span><?= e($v['name']) ?></span><?php endif; ?>
                <small><?= e(implode(' / ', array_filter([$v['size'], $v['color']])) ?: 'One size') ?> · <?= e($v['sku']) ?></small>
              </span>
              <span class="<?= (int) $v['stock'] === 0 ? 'stock-out' : 'stock-low' ?>"><?= (int) $v['stock'] === 0 ? 'Out' : (int) $v['stock'] . ' left' ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?><p class="a-card__body muted">Everything is comfortably stocked.</p><?php endif; ?>
    </section>
  </div>
</div>
