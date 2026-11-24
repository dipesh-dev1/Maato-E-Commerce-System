<div class="table-wrap">
  <table class="data-table">
    <thead><tr><?= sort_th('When', 'date', $filters) ?><th scope="col">Who</th><th scope="col">What</th><th scope="col">Action</th><th scope="col">Details</th><th scope="col">IP</th></tr></thead>
    <tbody>
      <?php foreach ($result['items'] as $a): ?>
        <tr>
          <td><?= e(format_date($a['created_at'], 'j M Y')) ?><span class="cell-sub"><?= e(format_date($a['created_at'], 'g:i:s a')) ?></span></td>
          <td><?= e($a['user_name'] ?? 'Deleted user') ?><?php if ($a['role_slug']): ?><span class="cell-sub"><?= e(ucfirst($a['role_slug'])) ?></span><?php endif; ?></td>
          <td class="wrap"><?= e($a['description']) ?></td>
          <td><span class="badge badge--plain"><?= e($a['action']) ?></span></td>
          <td class="wrap meta-json"><?= $a['meta'] ? e(str_limit($a['meta'], 140)) : '—' ?></td>
          <td class="muted"><?= e($a['ip_address']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?><?php partial('admin/partials/table-empty', ['cols' => 6, 'heading' => 'Nothing logged yet', 'body' => 'Changes made in the back office will appear here.']); ?><?php endif; ?>
    </tbody>
  </table>
</div>
<?php partial('admin/partials/table-foot', ['result' => $result, 'filters' => $filters, 'noun' => 'entry', 'nouns' => 'entries']); ?>
