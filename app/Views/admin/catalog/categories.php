<div class="page-head">
  <div><h1 class="h1">Categories</h1><p>Two levels: departments and their sections. Hidden categories disappear from menus but keep their products.</p></div>
  <button class="btn" type="button" data-new-record="category-form"><?= icon('plus', 'icon--sm') ?> New category</button>
</div>
<div class="a-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th scope="col">Name</th><th scope="col">Web address</th><th scope="col">Tagline</th><th scope="col" class="num">Products</th><th scope="col">Visible</th><th scope="col" class="num">Order</th><th scope="col" class="col-actions"><span class="visually-hidden">Actions</span></th></tr></thead>
      <tbody>
        <?php foreach ($tree as $root): ?>
          <?php foreach (array_merge([$root], $root['children']) as $c): $isChild = $c['parent_id'] !== null; ?>
            <tr>
              <td><span class="<?= $isChild ? 'indent' : '' ?>"><strong><?= e($c['name']) ?></strong></span></td>
              <td class="muted">/category/<?= e($c['slug']) ?></td>
              <td class="wrap muted"><?= e((string) $c['tagline']) ?></td>
              <td class="num"><?= (int) $c['product_count'] ?></td>
              <td><span class="badge badge--<?= $c['is_active'] ? 'success' : 'neutral' ?>"><?= $c['is_active'] ? 'Visible' : 'Hidden' ?></span></td>
              <td class="num"><?= (int) $c['sort_order'] ?></td>
              <td class="col-actions">
                <span class="row-actions">
                  <button class="icon-btn" type="button" aria-label="Edit <?= e($c['name']) ?>" data-edit-record="category-form" data-id="<?= (int) $c['id'] ?>"
                          data-record="<?= e(json_encode(['name' => $c['name'], 'slug' => $c['slug'], 'parent_id' => $c['parent_id'], 'tagline' => $c['tagline'], 'description' => $c['description'], 'sort_order' => $c['sort_order'], 'is_active' => (bool) $c['is_active']])) ?>"><?= icon('edit') ?></button>
                  <form method="post" action="<?= e(route('admin.categories.destroy', ['id' => $c['id']])) ?>" data-confirm="Delete the <?= e($c['name']) ?> category? This only works when it has no products or sub-categories.">
                    <?= csrf_field() ?><?= method_field('DELETE') ?>
                    <button class="icon-btn" type="submit" aria-label="Delete <?= e($c['name']) ?>"><?= icon('trash') ?></button>
                  </form>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<dialog class="drawer slideover" id="category-form" aria-labelledby="category-form-title">
  <form method="post" enctype="multipart/form-data" data-record-form data-create="<?= e(route('admin.categories.store')) ?>" data-update="<?= e(url('/admin/categories/__ID__')) ?>">
    <?php partial('admin/partials/slideover-head', ['id' => 'category-form', 'noun' => 'category']); ?>
    <div class="dialog__body">
      <?= csrf_field() ?><input type="hidden" name="_method" value="POST" data-method-field>
      <div class="field"><label class="label" for="c-name">Name</label><input class="input" id="c-name" name="name" required maxlength="100" data-slug-source><?php partial('admin/partials/field-error', ['name' => 'name']); ?></div>
      <div class="field"><label class="label" for="c-slug">Web address</label><div class="input-affix"><span>/category/</span><input class="input" id="c-slug" name="slug" maxlength="120" data-slug-target data-auto="1"></div><?php partial('admin/partials/field-error', ['name' => 'slug']); ?></div>
      <div class="field">
        <label class="label" for="c-parent">Parent</label>
        <select class="select" id="c-parent" name="parent_id">
          <option value="">None, top level</option>
          <?php foreach ($tree as $root): ?><option value="<?= (int) $root['id'] ?>"><?= e($root['name']) ?></option><?php endforeach; ?>
        </select>
        <?php partial('admin/partials/field-error', ['name' => 'parent_id']); ?>
      </div>
      <div class="field"><label class="label" for="c-tagline">Tagline <span class="optional">(optional)</span></label><input class="input" id="c-tagline" name="tagline" maxlength="160"><?php partial('admin/partials/field-error', ['name' => 'tagline']); ?></div>
      <div class="field"><label class="label" for="c-desc">Description <span class="optional">(optional)</span></label><textarea class="textarea" id="c-desc" name="description" rows="4" maxlength="2000"></textarea></div>
      <div class="field-row">
        <div class="field"><label class="label" for="c-order">Menu order</label><input class="input" id="c-order" name="sort_order" type="number" min="0" max="999" value="0"><?php partial('admin/partials/field-error', ['name' => 'sort_order']); ?></div>
        <div class="field"><span class="label">Visibility</span><label class="switch"><input type="checkbox" name="is_active" value="1" checked> Visible in the shop</label></div>
      </div>
      <div class="field"><label class="label" for="c-image">Cover image <span class="optional">(top-level only)</span></label><input class="file-input" id="c-image" type="file" name="image" accept="image/jpeg,image/png,image/webp"><?php partial('admin/partials/field-error', ['name' => 'image']); ?></div>
    </div>
    <?php partial('admin/partials/slideover-foot'); ?>
  </form>
</dialog>
