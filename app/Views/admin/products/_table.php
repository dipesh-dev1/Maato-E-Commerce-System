<?php $low = (int) setting('low_stock_threshold', 5); ?>
<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th scope="col" class="col-check"><label class="check"><input type="checkbox" data-select-all aria-label="Select all products on this page"></label></th>
        <?= sort_th('Product', 'name', $filters) ?>
        <th scope="col">Category</th>
        <?= sort_th('Status', 'status', $filters) ?>
        <?= sort_th('Price', 'price', $filters, 'num') ?>
        <?= sort_th('Stock', 'stock', $filters, 'num') ?>
        <?= sort_th('Updated', 'updated', $filters) ?>
        <th scope="col" class="col-actions"><span class="visually-hidden">Actions</span></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result['items'] as $p): $stock = (int) $p['stock_total']; ?>
        <tr>
          <td class="col-check"><label class="check"><input type="checkbox" name="ids[]" value="<?= (int) $p['id'] ?>" data-row-check aria-label="Select <?= e($p['name']) ?>"></label></td>
          <td>
            <div class="cell-product">
              <img src="<?= e(media($p['image'])) ?>" alt="" loading="lazy">
              <span><a href="<?= e(route('admin.products.edit', ['id' => $p['id']])) ?>"><?= e($p['name']) ?></a><span class="cell-sub"><?= e(str_limit((string) $p['skus'], 48)) ?> · <?= e(plural((int) $p['variant_count'], 'variant')) ?></span></span>
            </div>
          </td>
          <td><?= e($p['category_name']) ?></td>
          <td>
            <span class="badge badge--<?= $p['status'] === 'published' ? 'success' : 'neutral' ?>"><?= $p['status'] === 'published' ? 'Published' : 'Draft' ?></span>
            <?php if ($p['on_sale']): ?><span class="badge badge--accent badge--plain">Sale</span><?php endif; ?>
            <?php if ($p['is_featured']): ?><span class="badge badge--plain">Featured</span><?php endif; ?>
          </td>
          <td class="num"><?= e(money($p['price_min'])) ?><?= (float) $p['price_max'] > (float) $p['price_min'] ? '–' . e(money($p['price_max'], false)) : '' ?></td>
          <td class="num <?= $stock === 0 ? 'stock-out' : ($stock <= $low ? 'stock-low' : '') ?>"><?= $stock ?></td>
          <td><?= e(format_date($p['updated_at'], 'j M Y')) ?></td>
          <td class="col-actions">
            <span class="row-actions">
              <?php if ($p['status'] === 'published'): ?><a class="icon-btn" href="<?= e(route('product', ['slug' => $p['slug']])) ?>" target="_blank" rel="noopener" aria-label="View <?= e($p['name']) ?> in the shop"><?= icon('external') ?></a><?php endif; ?>
              <a class="icon-btn" href="<?= e(route('admin.products.edit', ['id' => $p['id']])) ?>" aria-label="Edit <?= e($p['name']) ?>"><?= icon('edit') ?></a>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?>
        <?php partial('admin/partials/table-empty', ['cols' => 8, 'heading' => 'No products match', 'body' => 'Try clearing the search or filters.']); ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php partial('admin/partials/table-foot', ['result' => $result, 'filters' => $filters, 'noun' => 'product']); ?>
