<div class="page-head">
  <div><h1 class="h1">Makers &amp; brands</h1><p>The workshops behind each piece. Shown on product pages as the maker and where they work.</p></div>
  <button class="btn" type="button" data-new-record="brand-form"><?= icon('plus', 'icon--sm') ?> New maker</button>
</div>
<div class="a-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th scope="col">Maker</th><th scope="col">Based in</th><th scope="col">About</th><th scope="col" class="num">Products</th><th scope="col">Status</th><th scope="col" class="col-actions"><span class="visually-hidden">Actions</span></th></tr></thead>
      <tbody>
        <?php foreach ($brands as $b): ?>
          <tr>
            <td><strong><?= e($b['name']) ?></strong></td>
            <td><?= e((string) $b['origin']) ?></td>
            <td class="wrap muted"><?= e(str_limit((string) $b['description'], 110)) ?></td>
            <td class="num"><?= (int) $b['product_count'] ?></td>
            <td><span class="badge badge--<?= $b['is_active'] ? 'success' : 'neutral' ?>"><?= $b['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td class="col-actions">
              <span class="row-actions">
                <button class="icon-btn" type="button" aria-label="Edit <?= e($b['name']) ?>" data-edit-record="brand-form" data-id="<?= (int) $b['id'] ?>"
                        data-record="<?= e(json_encode(['name' => $b['name'], 'slug' => $b['slug'], 'origin' => $b['origin'], 'description' => $b['description'], 'is_active' => (bool) $b['is_active']])) ?>"><?= icon('edit') ?></button>
                <form method="post" action="<?= e(route('admin.brands.destroy', ['id' => $b['id']])) ?>" data-confirm="Delete <?= e($b['name']) ?>? Their <?= e(plural((int) $b['product_count'], 'product')) ?> will stay in the shop without a maker.">
                  <?= csrf_field() ?><?= method_field('DELETE') ?>
                  <button class="icon-btn" type="submit" aria-label="Delete <?= e($b['name']) ?>"><?= icon('trash') ?></button>
                </form>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$brands): ?><?php partial('admin/partials/table-empty', ['cols' => 6, 'heading' => 'No makers yet', 'body' => 'Add the workshops you buy from.']); ?><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<dialog class="drawer slideover" id="brand-form" aria-labelledby="brand-form-title">
  <form method="post" data-record-form data-create="<?= e(route('admin.brands.store')) ?>" data-update="<?= e(url('/admin/brands/__ID__')) ?>">
    <?php partial('admin/partials/slideover-head', ['id' => 'brand-form', 'noun' => 'maker']); ?>
    <div class="dialog__body">
      <?= csrf_field() ?><input type="hidden" name="_method" value="POST" data-method-field>
      <div class="field"><label class="label" for="b-name">Name</label><input class="input" id="b-name" name="name" required maxlength="100" data-slug-source><?php partial('admin/partials/field-error', ['name' => 'name']); ?></div>
      <div class="field"><label class="label" for="b-slug">Slug</label><input class="input" id="b-slug" name="slug" maxlength="120" data-slug-target data-auto="1"><?php partial('admin/partials/field-error', ['name' => 'slug']); ?></div>
      <div class="field"><label class="label" for="b-origin">Based in</label><input class="input" id="b-origin" name="origin" maxlength="100" placeholder="Thimi, Bhaktapur"><?php partial('admin/partials/field-error', ['name' => 'origin']); ?></div>
      <div class="field"><label class="label" for="b-desc">About</label><textarea class="textarea" id="b-desc" name="description" rows="5" maxlength="2000"></textarea><?php partial('admin/partials/field-error', ['name' => 'description']); ?></div>
      <div class="field"><label class="switch"><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
    </div>
    <?php partial('admin/partials/slideover-foot'); ?>
  </form>
</dialog>
