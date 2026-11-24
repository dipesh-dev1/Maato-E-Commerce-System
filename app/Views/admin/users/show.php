<?php use App\Models\OrderStatus; ?>
<div class="page-head">
  <div>
    <h1 class="h1"><?= e($user['name']) ?> <span class="badge badge--<?= $user['is_blocked'] ? 'danger' : 'success' ?>"><?= $user['is_blocked'] ? 'Blocked' : 'Active' ?></span></h1>
    <p><?= e($user['email']) ?> · joined <?= e(format_date($user['created_at'], 'j F Y')) ?></p>
  </div>
  <?php if (!$isSelf): ?>
    <form method="post" action="<?= e(route('admin.users.block', ['id' => $user['id']])) ?>"
          data-confirm="<?= $user['is_blocked'] ? 'Let ' . e($user['name']) . ' sign in again?' : 'Block ' . e($user['name']) . '? They will be signed out and unable to sign in or order.' ?>">
      <?= csrf_field() ?>
      <button class="btn <?= $user['is_blocked'] ? '' : 'btn--danger' ?>" type="submit"><?= $user['is_blocked'] ? 'Unblock account' : 'Block account' ?></button>
    </form>
  <?php endif; ?>
</div>
<div class="grid-2">
  <section class="a-card" aria-labelledby="orders-title">
    <div class="a-card__head"><h2 id="orders-title">Order history</h2><span class="muted"><?= e(plural((int) $orders['total'], 'order')) ?></span></div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th scope="col">Order</th><th scope="col">Placed</th><th scope="col">Status</th><th scope="col" class="num">Total</th></tr></thead>
        <tbody>
          <?php foreach ($orders['items'] as $o): ?>
            <tr>
              <td><?php if (can('orders.manage')): ?><a href="<?= e(route('admin.orders.show', ['id' => $o['id']])) ?>"><?= e($o['order_number']) ?></a><?php else: ?><?= e($o['order_number']) ?><?php endif; ?></td>
              <td><?= e(format_date($o['created_at'], 'j M Y')) ?></td>
              <td><?php partial('partials/status-badge', ['status' => $o['status']]); ?></td>
              <td class="num"><?= e(money($o['grand_total'])) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$orders['items']): ?><?php partial('admin/partials/table-empty', ['cols' => 4, 'heading' => 'No orders yet', 'body' => '']); ?><?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php partial('admin/partials/table-foot', ['result' => $orders, 'filters' => [], 'noun' => 'order']); ?>
  </section>
  <div class="stack-gap">
    <section class="a-card" aria-labelledby="profile-title">
      <div class="a-card__head"><h2 id="profile-title">Profile</h2></div>
      <div class="a-card__body">
        <dl class="dl">
          <dt>Phone</dt><dd><?= e($user['phone'] ?: '—') ?></dd>
          <dt>Last sign-in</dt><dd><?= $user['last_login_at'] ? e(format_date($user['last_login_at'], 'j M Y, g:i a')) : 'Never' ?></dd>
          <dt>Addresses</dt><dd><?= count($addresses) ?></dd>
        </dl>
        <?php foreach ($addresses as $a): ?>
          <address class="small section--tight"><strong><?= e($a['label']) ?><?= $a['is_default'] ? ' (default)' : '' ?></strong><br><?= e($a['line1']) ?>, <?= e($a['city']) ?>, <?= e($a['district']) ?></address>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="a-card" aria-labelledby="role-title">
      <div class="a-card__head"><h2 id="role-title">Role</h2></div>
      <div class="a-card__body">
        <?php if ($isSelf): ?>
          <p class="muted">This is your own account. Another admin has to change your role, so nobody locks themselves out by accident.</p>
        <?php else: ?>
          <form method="post" action="<?= e(route('admin.users.role', ['id' => $user['id']])) ?>" class="stack" data-confirm="Change <?= e($user['name']) ?>'s role? Staff can manage products and orders; admins can do everything.">
            <?= csrf_field() ?>
            <?php foreach ($roles as $r): ?>
              <label class="check"><input type="radio" name="role" value="<?= e($r['slug']) ?>"<?= $r['slug'] === $user['role_slug'] ? ' checked' : '' ?>> <span><strong><?= e($r['name']) ?></strong><br><span class="muted"><?= e((string) $r['description']) ?></span></span></label>
            <?php endforeach; ?>
            <button class="btn btn--ghost" type="submit">Update role</button>
          </form>
        <?php endif; ?>
      </div>
    </section>
  </div>
</div>
