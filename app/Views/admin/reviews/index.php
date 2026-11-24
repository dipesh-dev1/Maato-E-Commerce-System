<div class="page-head"><div><h1 class="h1">Reviews</h1><p>Only customers whose order was delivered can review. Nothing appears in the shop until it's approved.</p></div></div>
<div class="a-card" data-table>
  <form class="toolbar" method="get" action="<?= e(route('admin.reviews')) ?>" data-table-filters>
    <?php partial('admin/partials/search', ['value' => $filters['q'], 'placeholder' => 'Product, customer or text']); ?>
    <label class="visually-hidden" for="f-status">Status</label>
    <select class="select" id="f-status" name="status">
      <?php foreach (['pending' => 'Waiting for review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All reviews'] as $v => $l): ?>
        <option value="<?= $v ?>"<?= $filters['status'] === $v ? ' selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>"><input type="hidden" name="dir" value="<?= e($filters['dir']) ?>">
  </form>
  <form method="post" action="<?= e(route('admin.reviews.bulk')) ?>" data-bulk-form data-confirm-when="delete" data-confirm="Delete the selected reviews permanently?">
    <?= csrf_field() ?>
    <input type="hidden" name="return_status" value="<?= e($filters['status']) ?>">
    <div class="bulk-bar" data-bulk-bar hidden>
      <span data-bulk-count>0 selected</span>
      <label class="visually-hidden" for="bulk-action">Bulk action</label>
      <select class="select" id="bulk-action" name="action" required>
        <option value="">Choose an action…</option>
        <option value="approve">Approve</option>
        <option value="reject">Reject</option>
        <option value="delete">Delete</option>
      </select>
      <button class="btn btn--sm" type="submit">Apply</button>
    </div>
    <div data-table-body><?php partial('admin/reviews/_table', get_defined_vars()); ?></div>
  </form>
</div>
