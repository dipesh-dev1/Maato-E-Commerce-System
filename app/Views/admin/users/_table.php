<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <?= sort_th('Name', 'name', $filters) ?>
        <th scope="col">Role</th>
        <?= sort_th('Orders', 'orders', $filters, 'num') ?>
        <?= sort_th('Spent', 'spent', $filters, 'num') ?>
        <?= sort_th('Joined', 'created', $filters) ?>
        <?= sort_th('Last sign-in', 'login', $filters) ?>
        <th scope="col">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result['items'] as $u): ?>
        <tr>
          <td><a href="<?= e(route('admin.users.show', ['id' => $u['id']])) ?>"><strong><?= e($u['name']) ?></strong></a><span class="cell-sub"><?= e($u['email']) ?><?= $u['phone'] ? ' · ' . e($u['phone']) : '' ?></span></td>
          <td><span class="badge badge--<?= ['admin' => 'accent', 'staff' => 'info'][$u['role_slug']] ?? 'neutral' ?> badge--plain"><?= e($u['role_name']) ?></span></td>
          <td class="num"><?= (int) $u['order_count'] ?></td>
          <td class="num"><?= e(money($u['total_spent'])) ?></td>
          <td><?= e(format_date($u['created_at'])) ?></td>
          <td><?= $u['last_login_at'] ? e(format_date($u['last_login_at'], 'j M Y, g:i a')) : 'Never' ?></td>
          <td><span class="badge badge--<?= $u['is_blocked'] ? 'danger' : 'success' ?>"><?= $u['is_blocked'] ? 'Blocked' : 'Active' ?></span></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?><?php partial('admin/partials/table-empty', ['cols' => 7, 'heading' => 'No one matches', 'body' => 'Try part of their email or phone number.']); ?><?php endif; ?>
    </tbody>
  </table>
</div>
<?php partial('admin/partials/table-foot', ['result' => $result, 'filters' => $filters, 'noun' => 'account']); ?>
