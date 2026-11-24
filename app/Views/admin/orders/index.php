<?php use App\Models\OrderStatus; ?>
<div class="page-head">
  <div><h1 class="h1">Orders</h1><p><?= (int) ($counts['pending'] ?? 0) ?> pending, <?= (int) ($counts['confirmed'] ?? 0) + (int) ($counts['packed'] ?? 0) ?> being prepared, <?= (int) ($counts['shipped'] ?? 0) ?> out for delivery.</p></div>
  <a class="btn btn--ghost" href="<?= e(route('admin.orders.export') . '?' . http_build_query(array_filter($filters))) ?>" data-export-link><?= icon('download', 'icon--sm') ?> Export CSV</a>
</div>
<div class="a-card" data-table>
  <form class="toolbar" method="get" action="<?= e(route('admin.orders')) ?>" data-table-filters>
    <?php partial('admin/partials/search', ['value' => $filters['q'], 'placeholder' => 'Order number, name, email or phone']); ?>
    <label class="visually-hidden" for="f-status">Status</label>
    <select class="select" id="f-status" name="status">
      <option value="">Any status</option>
      <?php foreach (OrderStatus::LABELS as $v => $l): ?><option value="<?= $v ?>"<?= $filters['status'] === $v ? ' selected' : '' ?>><?= $l ?> (<?= (int) ($counts[$v] ?? 0) ?>)</option><?php endforeach; ?>
    </select>
    <label class="visually-hidden" for="f-payment">Payment</label>
    <select class="select" id="f-payment" name="payment">
      <option value="">Any payment</option>
      <?php foreach (OrderStatus::PAYMENT_LABELS as $v => $l): ?><option value="<?= $v ?>"<?= $filters['payment'] === $v ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
    </select>
    <label class="visually-hidden" for="f-from">From date</label><input class="input" id="f-from" type="date" name="from" value="<?= e($filters['from']) ?>" title="From">
    <label class="visually-hidden" for="f-to">To date</label><input class="input" id="f-to" type="date" name="to" value="<?= e($filters['to']) ?>" title="To">
    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>"><input type="hidden" name="dir" value="<?= e($filters['dir']) ?>">
    <noscript><button class="btn btn--ghost" type="submit">Filter</button></noscript>
  </form>
  <form method="post" action="<?= e(route('admin.orders.bulk')) ?>" data-bulk-form>
    <?= csrf_field() ?>
    <div class="bulk-bar" data-bulk-bar hidden>
      <span data-bulk-count>0 selected</span>
      <label class="visually-hidden" for="bulk-action">Mark selected as</label>
      <select class="select" id="bulk-action" name="action" required>
        <option value="">Mark as…</option>
        <?php foreach (['confirmed', 'packed', 'shipped', 'delivered'] as $s): ?><option value="<?= $s ?>"><?= OrderStatus::label($s) ?></option><?php endforeach; ?>
      </select>
      <button class="btn btn--sm" type="submit">Apply</button>
    </div>
    <div data-table-body><?php partial('admin/orders/_table', get_defined_vars()); ?></div>
  </form>
</div>
