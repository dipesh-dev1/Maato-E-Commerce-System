<?php
/** @var array $filters @var array $result @var array $tree @var ?array $category @var ?array $parent */
$crumbs = [];
if ($isSearch) {
    $crumbs[] = ['Search', null];
} else {
    $crumbs[] = ['Shop', route('shop')];
    if ($parent) {
        $crumbs[] = [$parent['name'], route('category', ['slug' => $parent['slug']])];
    }
    if ($category) {
        $crumbs[] = [$category['name'], null];
    }
}
$action = $isSearch ? route('search') : ($category ? route('category', ['slug' => $category['slug']]) : route('shop'));
// Sibling/child links shown as chips under the heading.
$branch = null;
foreach ($tree as $root) {
    if ($category && ((int) $root['id'] === (int) $category['id'] || (int) $root['id'] === (int) ($category['parent_id'] ?? 0))) {
        $branch = $root;
    }
}
?>
<?php partial('partials/breadcrumb', ['crumbs' => $crumbs]); ?>

<div class="container">
  <header class="listing-head">
    <div>
      <p class="eyebrow"><?= $isSearch ? 'Search' : ($parent ? e($parent['name']) : 'Maato shop') ?></p>
      <h1 class="h1"><?= e($title) ?></h1>
    </div>
    <?php if (!empty($intro)): ?><p><?= e($intro) ?></p><?php endif; ?>
  </header>

  <?php if ($branch && $branch['children']): ?>
    <nav class="subnav" aria-label="<?= e($branch['name']) ?> sections">
      <a class="chip" href="<?= e(route('category', ['slug' => $branch['slug']])) ?>"<?= (int) $category['id'] === (int) $branch['id'] ? ' aria-current="page"' : '' ?>>All <?= e(mb_strtolower($branch['name'])) ?></a>
      <?php foreach ($branch['children'] as $child): ?>
        <a class="chip" href="<?= e(route('category', ['slug' => $child['slug']])) ?>"<?= (int) $category['id'] === (int) $child['id'] ? ' aria-current="page"' : '' ?>><?= e($child['name']) ?></a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>

  <div class="listing">
    <details class="filters" open data-filters>
      <summary><?= icon('sliders') ?> Filter &amp; sort</summary>
      <form action="<?= e($action) ?>" method="get" data-filter-form>
        <?php if ($isSearch): ?>
          <div class="filters__group">
            <label class="filters__title" for="filter-q">Search</label>
            <input class="input" id="filter-q" type="search" name="q" value="<?= e($filters['q']) ?>" maxlength="80">
          </div>
        <?php endif; ?>

        <?php if (!$isSearch): ?>
          <div class="filters__group">
            <p class="filters__title">Category</p>
            <ul class="filters__links">
              <li><a href="<?= e(route('shop')) ?>"<?= !$category ? ' aria-current="page"' : '' ?>>Everything</a></li>
              <?php foreach ($tree as $root): ?>
                <li><a href="<?= e(route('category', ['slug' => $root['slug']])) ?>"<?= $category && (int) $category['id'] === (int) $root['id'] ? ' aria-current="page"' : '' ?>><?= e($root['name']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="filters__group">
          <p class="filters__title" id="price-label">Price (<?= e(setting('currency_symbol', 'Rs.')) ?>)</p>
          <div class="filters__price" role="group" aria-labelledby="price-label">
            <label class="visually-hidden" for="filter-min">Minimum price</label>
            <input class="input" id="filter-min" type="number" name="min" min="0" step="100" inputmode="numeric" placeholder="0" value="<?= e($filters['min'] ?? '') ?>">
            <span aria-hidden="true">–</span>
            <label class="visually-hidden" for="filter-max">Maximum price</label>
            <input class="input" id="filter-max" type="number" name="max" min="0" step="100" inputmode="numeric" placeholder="<?= e(number_format($priceCeiling, 0, '.', '')) ?>" value="<?= e($filters['max'] ?? '') ?>">
          </div>
        </div>

        <div class="filters__group stack stack--tight">
          <p class="filters__title">Availability</p>
          <label class="check"><input type="checkbox" name="in_stock" value="1"<?= $filters['in_stock'] ? ' checked' : '' ?>> In stock only</label>
          <label class="check"><input type="checkbox" name="on_sale" value="1"<?= $filters['on_sale'] ? ' checked' : '' ?>> Reduced pieces</label>
        </div>

        <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>" data-sort-mirror>

        <div class="filters__actions">
          <button class="btn btn--sm filters__apply" type="submit">Show results</button>
          <a class="link-btn" href="<?= e($action . ($isSearch ? '?q=' . rawurlencode($filters['q']) : '')) ?>">Clear filters</a>
        </div>
      </form>
    </details>

    <div>
      <div class="listing__toolbar">
        <p class="listing__count" data-result-count aria-live="polite"><?= e(plural($result['total'], 'piece')) ?></p>
        <div class="listing__sort">
          <label for="sort-select">Sort</label>
          <select class="select" id="sort-select" data-sort-select>
            <?php foreach ($sorts as $value => $label): ?>
              <option value="<?= e($value) ?>"<?= $filters['sort'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="listing__results" data-listing-results>
        <?php partial('store/partials/listing-results', get_defined_vars()); ?>
      </div>
    </div>
  </div>
</div>
