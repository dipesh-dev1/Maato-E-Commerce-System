<?php $dt = static fn (?string $v) => $v ? date('Y-m-d\TH:i', strtotime($v)) : ''; ?>
<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <?= sort_th('Code', 'code', $filters) ?>
        <th scope="col">Discount</th>
        <th scope="col" class="num">Minimum</th>
        <?= sort_th('Used', 'used', $filters, 'num') ?>
        <?= sort_th('Expires', 'expires', $filters) ?>
        <th scope="col">State</th>
        <th scope="col" class="col-actions"><span class="visually-hidden">Actions</span></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result['items'] as $c):
          $expired = $c['expires_at'] && strtotime($c['expires_at']) <= time();
          $usedUp = $c['usage_limit'] !== null && (int) $c['used_count'] >= (int) $c['usage_limit'];
          $scheduled = $c['starts_at'] && strtotime($c['starts_at']) > time();
          [$tone, $state] = !$c['is_active'] ? ['neutral', 'Off'] : ($expired ? ['neutral', 'Expired'] : ($usedUp ? ['warning', 'Used up'] : ($scheduled ? ['info', 'Scheduled'] : ['success', 'Live'])));
      ?>
        <tr>
          <td><strong><?= e($c['code']) ?></strong><?php if ($c['description']): ?><span class="cell-sub"><?= e($c['description']) ?></span><?php endif; ?></td>
          <td><?= $c['type'] === 'percent' ? e(rtrim(rtrim($c['value'], '0'), '.')) . '% off' : e(money($c['value'])) . ' off' ?></td>
          <td class="num"><?= (float) $c['min_cart_value'] > 0 ? e(money($c['min_cart_value'])) : '—' ?></td>
          <td class="num"><?= (int) $c['used_count'] ?><?= $c['usage_limit'] !== null ? ' / ' . (int) $c['usage_limit'] : '' ?></td>
          <td><?= $c['expires_at'] ? e(format_date($c['expires_at'], 'j M Y')) : 'Never' ?></td>
          <td><span class="badge badge--<?= $tone ?>"><?= $state ?></span></td>
          <td class="col-actions">
            <span class="row-actions">
              <button class="icon-btn" type="button" aria-label="Edit <?= e($c['code']) ?>" data-edit-record="coupon-form" data-id="<?= (int) $c['id'] ?>"
                      data-record="<?= e(json_encode(['code' => $c['code'], 'description' => $c['description'], 'type' => $c['type'], 'value' => $c['value'], 'min_cart_value' => $c['min_cart_value'], 'usage_limit' => $c['usage_limit'], 'starts_at' => $dt($c['starts_at']), 'expires_at' => $dt($c['expires_at']), 'is_active' => (bool) $c['is_active']])) ?>"><?= icon('edit') ?></button>
              <form method="post" action="<?= e(route('admin.coupons.destroy', ['id' => $c['id']])) ?>" data-confirm="Delete coupon <?= e($c['code']) ?>? Customers will no longer be able to use it.">
                <?= csrf_field() ?><?= method_field('DELETE') ?>
                <button class="icon-btn" type="submit" aria-label="Delete <?= e($c['code']) ?>"><?= icon('trash') ?></button>
              </form>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?><?php partial('admin/partials/table-empty', ['cols' => 7, 'heading' => 'No coupons match', 'body' => 'Create one for the next festival.']); ?><?php endif; ?>
    </tbody>
  </table>
</div>
<?php partial('admin/partials/table-foot', ['result' => $result, 'filters' => $filters, 'noun' => 'coupon']); ?>
