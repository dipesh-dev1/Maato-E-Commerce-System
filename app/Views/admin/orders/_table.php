<?php use App\Models\OrderStatus; ?>
<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th scope="col" class="col-check"><label class="check"><input type="checkbox" data-select-all aria-label="Select all orders on this page"></label></th>
        <?= sort_th('Order', 'number', $filters) ?>
        <?= sort_th('Placed', 'date', $filters) ?>
        <?= sort_th('Customer', 'customer', $filters) ?>
        <?= sort_th('Status', 'status', $filters) ?>
        <th scope="col">Payment</th>
        <th scope="col" class="num">Items</th>
        <?= sort_th('Total', 'total', $filters, 'num') ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result['items'] as $o): ?>
        <tr>
          <td class="col-check"><label class="check"><input type="checkbox" name="ids[]" value="<?= (int) $o['id'] ?>" data-row-check aria-label="Select order <?= e($o['order_number']) ?>"></label></td>
          <td><a href="<?= e(route('admin.orders.show', ['id' => $o['id']])) ?>"><strong><?= e($o['order_number']) ?></strong></a></td>
          <td><?= e(format_date($o['created_at'], 'j M Y')) ?><span class="cell-sub"><?= e(format_date($o['created_at'], 'g:i a')) ?></span></td>
          <td><?= e($o['customer_name']) ?><span class="cell-sub"><?= e($o['ship_city']) ?></span></td>
          <td><?php partial('partials/status-badge', ['status' => $o['status']]); ?></td>
          <td><?= e(OrderStatus::PAYMENT_LABELS[$o['payment_method']]) ?></td>
          <td class="num"><?= (int) $o['item_count'] ?></td>
          <td class="num"><?= e(money($o['grand_total'])) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?><?php partial('admin/partials/table-empty', ['cols' => 8, 'heading' => 'No orders match', 'body' => 'Try a wider date range or clear the filters.']); ?><?php endif; ?>
    </tbody>
  </table>
</div>
<?php partial('admin/partials/table-foot', ['result' => $result, 'filters' => $filters, 'noun' => 'order']); ?>
