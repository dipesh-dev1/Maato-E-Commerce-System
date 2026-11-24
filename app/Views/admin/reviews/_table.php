<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th scope="col" class="col-check"><label class="check"><input type="checkbox" data-select-all aria-label="Select all reviews on this page"></label></th>
        <?= sort_th('Rating', 'rating', $filters) ?>
        <th scope="col">Review</th>
        <?= sort_th('Product', 'product', $filters) ?>
        <th scope="col">Customer</th>
        <?= sort_th('Status', 'status', $filters) ?>
        <?= sort_th('Date', 'date', $filters) ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result['items'] as $r): ?>
        <tr>
          <td class="col-check"><label class="check"><input type="checkbox" name="ids[]" value="<?= (int) $r['id'] ?>" data-row-check aria-label="Select review by <?= e($r['author']) ?>"></label></td>
          <td><span aria-label="<?= (int) $r['rating'] ?> out of 5"><?= str_repeat('★', (int) $r['rating']) ?><span class="muted"><?= str_repeat('★', 5 - (int) $r['rating']) ?></span></span></td>
          <td class="wrap"><strong><?= e($r['title']) ?></strong><span class="cell-sub"><?= e(str_limit($r['body'], 160)) ?></span></td>
          <td><a href="<?= e(route('product', ['slug' => $r['product_slug']])) ?>#reviews" target="_blank" rel="noopener"><?= e($r['product_name']) ?></a></td>
          <td><?= e($r['author']) ?><span class="cell-sub"><?= e($r['author_email']) ?></span></td>
          <td><span class="badge badge--<?= ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'neutral'][$r['status']] ?>"><?= e(ucfirst($r['status'])) ?></span></td>
          <td><?= e(format_date($r['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?><?php partial('admin/partials/table-empty', ['cols' => 7, 'heading' => $filters['status'] === 'pending' ? 'All caught up' : 'No reviews match', 'body' => $filters['status'] === 'pending' ? 'Nothing is waiting for moderation.' : 'Try another status.']); ?><?php endif; ?>
    </tbody>
  </table>
</div>
<?php partial('admin/partials/table-foot', ['result' => $result, 'filters' => $filters, 'noun' => 'review']); ?>
