<div class="page-head"><div><h1 class="h1">Users</h1><p>Customers, staff and admins. Blocking someone signs them out immediately; their orders stay intact.</p></div></div>
<div class="a-card" data-table>
  <form class="toolbar" method="get" action="<?= e(route('admin.users')) ?>" data-table-filters>
    <?php partial('admin/partials/search', ['value' => $filters['q'], 'placeholder' => 'Name, email or phone']); ?>
    <label class="visually-hidden" for="f-role">Role</label>
    <select class="select" id="f-role" name="role">
      <option value="">All roles</option>
      <?php foreach ($roles as $r): ?><option value="<?= e($r['slug']) ?>"<?= $filters['role'] === $r['slug'] ? ' selected' : '' ?>><?= e($r['name']) ?></option><?php endforeach; ?>
    </select>
    <label class="visually-hidden" for="f-state">Account state</label>
    <select class="select" id="f-state" name="state">
      <option value="">Any state</option>
      <option value="blocked"<?= $filters['state'] === 'blocked' ? ' selected' : '' ?>>Blocked</option>
    </select>
    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>"><input type="hidden" name="dir" value="<?= e($filters['dir']) ?>">
  </form>
  <div data-table-body><?php partial('admin/users/_table', get_defined_vars()); ?></div>
</div>
