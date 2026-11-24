<?php
/** @var ?array $product @var array $variants @var array $images @var array $categories @var array $brands */
$p = $product ?? [];
$v = static fn (string $k, $default = '') => old($k, $p[$k] ?? $default);
$amount = static fn (string $k) => preg_replace('/\.0+$/', '', (string) $v($k));
$dt = static fn (?string $value) => $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
$rows = old('variants', null);
if (!is_array($rows)) {
    $rows = $variants ?: [['id' => '', 'sku' => '', 'size' => '', 'color' => '', 'color_hex' => '', 'price' => '', 'sale_price' => '', 'stock' => 0]];
}
$warnings = App\Core\Session::getFlash('upload_warnings', []);
$action = $product ? route('admin.products.update', ['id' => $product['id']]) : route('admin.products.store');
?>
<div class="page-head">
  <div>
    <h1 class="h1"><?= $product ? e($product['name']) : 'New product' ?></h1>
    <p><?= $product ? 'Last updated ' . e(format_date($product['updated_at'], 'j M Y, g:i a')) : 'Fill in the essentials first; you can save as a draft and come back.' ?></p>
  </div>
  <div class="cluster">
    <?php if ($product && $product['status'] === 'published'): ?>
      <a class="btn btn--ghost" href="<?= e(route('product', ['slug' => $product['slug']])) ?>" target="_blank" rel="noopener"><?= icon('external', 'icon--sm') ?> View in shop</a>
    <?php endif; ?>
    <button class="btn" type="submit" form="product-form">Save product</button>
  </div>
</div>

<?php if ($errors = App\Core\Session::getFlash('errors', [])): ?>
  <p class="form-error" role="alert"><?= icon('alert') ?> <span>Please fix <?= e(plural(count($errors), 'problem')) ?> below<?= isset($errors['variants']) ? ': ' . e($errors['variants']) : '' ?>.</span></p>
<?php endif; ?>
<?php foreach ($warnings as $w): ?><p class="notice notice--warning"><?= icon('alert') ?><span><?= e($w) ?></span></p><?php endforeach; ?>

<form id="product-form" class="editor" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-product-form>
  <?= csrf_field() ?>
  <?php if ($product): ?><?= method_field('PATCH') ?><?php endif; ?>
  <div class="stack-gap">
    <section class="a-card a-card__body">
      <div class="form-section">
        <h3>Basics</h3>
        <div class="field">
          <label class="label" for="name">Name</label>
          <input class="input" id="name" name="name" required maxlength="160" value="<?= e($v('name')) ?>" data-slug-source<?= aria_invalid('name') ?>>
          <?= field_error('name') ?>
        </div>
        <div class="field">
          <label class="label" for="slug">Web address</label>
          <div class="input-affix"><span>/product/</span><input class="input" id="slug" name="slug" maxlength="180" value="<?= e($v('slug')) ?>" pattern="[a-z0-9]+(-[a-z0-9]+)*" data-slug-target<?= $product ? '' : ' data-auto="1"' ?><?= aria_invalid('slug') ?>></div>
          <p class="hint">Lowercase words joined by hyphens. Leave blank to make one from the name.</p>
          <?= field_error('slug') ?>
        </div>
        <div class="field">
          <label class="label" for="short_description">One-line summary</label>
          <input class="input" id="short_description" name="short_description" required maxlength="255" value="<?= e($v('short_description')) ?>"<?= aria_invalid('short_description') ?>>
          <p class="hint">Shown under the name on product pages and in search.</p>
          <?= field_error('short_description') ?>
        </div>
        <div class="field">
          <label class="label" for="description">Description</label>
          <textarea class="textarea" id="description" name="description" rows="7" required maxlength="10000"<?= aria_invalid('description') ?>><?= e($v('description')) ?></textarea>
          <p class="hint">Leave a blank line between paragraphs.</p>
          <?= field_error('description') ?>
        </div>
        <div class="field">
          <label class="label" for="details">Materials, size &amp; care <span class="optional">(one per line)</span></label>
          <textarea class="textarea" id="details" name="details" rows="4" maxlength="3000"><?= e($v('details')) ?></textarea>
          <?= field_error('details') ?>
        </div>
      </div>

      <div class="form-section">
        <h3>Photos</h3>
        <p>The first photo is the main image; the second appears when shoppers hover. Drag to reorder.</p>
        <ul class="image-list" data-image-list>
          <?php foreach ($images as $i => $img): ?>
            <li class="image-tile" draggable="true" data-image-tile>
              <input type="hidden" name="image_order[]" value="<?= (int) $img['id'] ?>">
              <img src="<?= e(media($img['thumb_path'])) ?>" alt="<?= e($img['alt']) ?>">
              <span class="image-tile__badge badge badge--plain" data-main-badge<?= $i === 0 ? '' : ' hidden' ?>>Main</span>
              <div class="image-tile__bar">
                <span>
                  <button class="icon-btn" type="button" data-move="-1" aria-label="Move earlier"><?= icon('chevron-left') ?></button>
                  <button class="icon-btn" type="button" data-move="1" aria-label="Move later"><?= icon('chevron-right') ?></button>
                </span>
                <label class="icon-btn" title="Remove on save">
                  <input class="visually-hidden" type="checkbox" name="remove_images[]" value="<?= (int) $img['id'] ?>" data-remove-image>
                  <?= icon('trash') ?><span class="visually-hidden">Remove this photo when saving</span>
                </label>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <label class="dropzone section--tight" data-dropzone>
          <?= icon('upload', 'icon--lg') ?>
          <span><strong>Add photos</strong> or drop them here</span>
          <span class="hint">JPG, PNG or WebP, up to <?= (int) $maxMb ?> MB each. Portrait 4:5 looks best.</span>
          <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple data-image-input>
        </label>
        <ul class="image-list section--tight" data-image-previews aria-live="polite"></ul>
      </div>

      <div class="form-section">
        <h3>Variants &amp; stock</h3>
        <p>One row per size/colour combination. Leave price blank to use the product price.</p>
        <div class="table-wrap">
          <table class="variants-table">
            <colgroup><col class="c-sku"><col class="c-opt"><col class="c-opt"><col class="c-hex"><col class="c-num"><col class="c-num"><col class="c-stock"><col class="c-del"></colgroup>
            <thead><tr><th>SKU</th><th>Size</th><th>Colour</th><th><span class="visually-hidden">Swatch</span></th><th>Price</th><th>Sale price</th><th>Stock</th><th><span class="visually-hidden">Remove</span></th></tr></thead>
            <tbody data-variant-rows>
              <?php foreach (array_values($rows) as $i => $row): ?>
                <?php partial('admin/products/_variant-row', ['i' => $i, 'row' => $row]); ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <template data-variant-template><?php partial('admin/products/_variant-row', ['i' => '__INDEX__', 'row' => []]); ?></template>
        <p class="section--tight"><button class="btn btn--ghost btn--sm" type="button" data-add-variant><?= icon('plus', 'icon--sm') ?> Add a variant</button></p>
        <?= field_error('variants') ?>
      </div>

      <div class="form-section">
        <h3>Search engines</h3>
        <div class="field">
          <label class="label" for="meta_title">Page title <span class="optional">(optional)</span></label>
          <input class="input" id="meta_title" name="meta_title" maxlength="160" value="<?= e($v('meta_title')) ?>">
        </div>
        <div class="field">
          <label class="label" for="meta_description">Description <span class="optional">(optional)</span></label>
          <input class="input" id="meta_description" name="meta_description" maxlength="255" value="<?= e($v('meta_description')) ?>">
        </div>
      </div>
    </section>
  </div>

  <aside class="editor__side">
    <section class="a-card a-card__body">
      <div class="form-section">
        <h3>Visibility</h3>
        <div class="field">
          <label class="label" for="status">Status</label>
          <select class="select" id="status" name="status">
            <option value="draft"<?= $v('status', 'draft') === 'draft' ? ' selected' : '' ?>>Draft: hidden from the shop</option>
            <option value="published"<?= $v('status') === 'published' ? ' selected' : '' ?>>Published</option>
          </select>
        </div>
        <label class="switch section--tight"><input type="checkbox" name="is_featured" value="1"<?= $v('is_featured') ? ' checked' : '' ?>> Feature on the home page</label>
      </div>
      <div class="form-section">
        <h3>Organisation</h3>
        <div class="field">
          <label class="label" for="category_id">Category</label>
          <select class="select" id="category_id" name="category_id" required<?= aria_invalid('category_id') ?>>
            <option value="">Choose…</option>
            <?php foreach ($categories as $root): ?>
              <option value="<?= (int) $root['id'] ?>"<?= (string) $v('category_id') === (string) $root['id'] ? ' selected' : '' ?>><?= e($root['name']) ?></option>
              <?php foreach ($root['children'] as $child): ?>
                <option value="<?= (int) $child['id'] ?>"<?= (string) $v('category_id') === (string) $child['id'] ? ' selected' : '' ?>>&nbsp;&nbsp;— <?= e($child['name']) ?></option>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </select>
          <?= field_error('category_id') ?>
        </div>
        <div class="field">
          <label class="label" for="brand_id">Maker</label>
          <select class="select" id="brand_id" name="brand_id">
            <option value="">None</option>
            <?php foreach ($brands as $b): ?><option value="<?= (int) $b['id'] ?>"<?= (string) $v('brand_id') === (string) $b['id'] ? ' selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
          </select>
          <?= field_error('brand_id') ?>
        </div>
        <div class="field">
          <label class="label" for="size_guide">Size guide</label>
          <select class="select" id="size_guide" name="size_guide">
            <option value="">None</option>
            <?php foreach (['bedding' => 'Bedding', 'rug' => 'Rugs', 'furniture' => 'Furniture'] as $k => $l): ?><option value="<?= $k ?>"<?= $v('size_guide') === $k ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-section">
        <h3>Pricing</h3>
        <div class="field">
          <label class="label" for="price">Price (<?= e(setting('currency_symbol', 'Rs.')) ?>)</label>
          <input class="input" id="price" name="price" type="number" min="0" step="0.01" required value="<?= e($amount('price')) ?>"<?= aria_invalid('price') ?>>
          <?= field_error('price') ?>
        </div>
        <div class="field">
          <label class="label" for="sale_price">Sale price <span class="optional">(optional)</span></label>
          <input class="input" id="sale_price" name="sale_price" type="number" min="0" step="0.01" value="<?= e($amount('sale_price')) ?>"<?= aria_invalid('sale_price') ?>>
          <?= field_error('sale_price') ?>
        </div>
        <div class="field">
          <label class="label" for="sale_starts_at">Sale starts</label>
          <input class="input" id="sale_starts_at" name="sale_starts_at" type="datetime-local" value="<?= e(old('sale_starts_at', $dt($p['sale_starts_at'] ?? null))) ?>">
          <?= field_error('sale_starts_at') ?>
        </div>
        <div class="field">
          <label class="label" for="sale_ends_at">Sale ends</label>
          <input class="input" id="sale_ends_at" name="sale_ends_at" type="datetime-local" value="<?= e(old('sale_ends_at', $dt($p['sale_ends_at'] ?? null))) ?>"<?= aria_invalid('sale_ends_at') ?>>
          <p class="hint">Leave both blank for a sale with no end date.</p>
          <?= field_error('sale_ends_at') ?>
        </div>
      </div>
      <button class="btn btn--block" type="submit">Save product</button>
    </section>
    <?php if ($product): ?>
      <button class="btn btn--ghost btn--block" type="submit" form="delete-product"><?= icon('trash', 'icon--sm') ?> Delete product</button>
    <?php endif; ?>
  </aside>
</form>
<?php if ($product): ?>
  <form id="delete-product" method="post" action="<?= e(route('admin.products.destroy', ['id' => $product['id']])) ?>" data-confirm="Delete <?= e($product['name']) ?>? It disappears from the shop and from carts. Past orders keep their copy.">
    <?= csrf_field() ?><?= method_field('DELETE') ?>
  </form>
<?php endif; ?>
