<div class="page-head">
  <div><h1 class="h1">Products</h1><p>Everything in the catalogue, including drafts. Deleted products stay on past orders.</p></div>
  <a class="btn" href="<?= e(route('admin.products.create')) ?>"><?= icon('plus', 'icon--sm') ?> New product</a>
</div>
<div class="a-card" data-table>
  <form class="toolbar" method="get" action="<?= e(route('admin.products')) ?>" data-table-filters>
    <?php partial('admin/partials/search', ['value' => $filters['q'], 'placeholder' => 'Search name or SKU']); ?>
    <label class="visually-hidden" for="f-status">Status</label>
    <select class="select" id="f-status" name="status">
      <option value="">Any status</option>
      <?php foreach (['published' => 'Published', 'draft' => 'Draft'] as $v => $l): ?><option value="<?= $v ?>"<?= $filters['status'] === $v ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
    </select>
    <label class="visually-hidden" for="f-category">Category</label>
    <select class="select" id="f-category" name="category">
      <option value="">All categories</option>
      <?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>"<?= $filters['category'] === (string) $c['id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
    </select>
    <label class="visually-hidden" for="f-stock">Stock</label>
    <select class="select" id="f-stock" name="stock">
      <option value="">Any stock</option>
      <option value="low"<?= $filters['stock'] === 'low' ? ' selected' : '' ?>>Low stock</option>
      <option value="out"<?= $filters['stock'] === 'out' ? ' selected' : '' ?>>Out of stock</option>
    </select>
    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>"><input type="hidden" name="dir" value="<?= e($filters['dir']) ?>">
    <noscript><button class="btn btn--ghost" type="submit">Filter</button></noscript>
  </form>
  <form method="post" action="<?= e(route('admin.products.bulk')) ?>" data-bulk-form data-confirm-when="delete" data-confirm="Delete the selected products? They disappear from the shop; past orders keep their copy.">
    <?= csrf_field() ?>
    <div class="bulk-bar" data-bulk-bar hidden>
      <span data-bulk-count>0 selected</span>
      <label class="visually-hidden" for="bulk-action">Bulk action</label>
      <select class="select" id="bulk-action" name="action" required>
        <option value="">Choose an action…</option>
        <option value="publish">Publish</option>
        <option value="draft">Move to drafts</option>
        <option value="delete">Delete</option>
      </select>
      <button class="btn btn--sm" type="submit">Apply</button>
    </div>
    <div data-table-body><?php partial('admin/products/_table', get_defined_vars()); ?></div>
  </form>
</div>
