<?php
/** Grid + pagination, rendered on first load and returned as HTML by the filter endpoint. */
$query = array_filter([
    'q' => $filters['q'] ?: null, 'min' => $filters['min'], 'max' => $filters['max'],
    'in_stock' => $filters['in_stock'] ? 1 : null, 'on_sale' => $filters['on_sale'] ? 1 : null,
    'sort' => $filters['sort'] !== 'featured' ? $filters['sort'] : null,
], static fn ($v) => $v !== null);
?>
<?php if ($result['items']): ?>
  <div class="product-grid product-grid--3">
    <?php foreach ($result['items'] as $i => $p): ?>
      <?php partial('partials/product-card', ['p' => $p, 'wishlist' => $wishlist, 'eager' => $i < 3]); ?>
    <?php endforeach; ?>
  </div>
  <div class="listing__pagination">
    <?php partial('partials/pagination', ['result' => $result, 'query' => $query]); ?>
  </div>
<?php elseif ($isSearch && $filters['q'] === ''): ?>
  <?php partial('partials/empty', [
      'icon' => 'search', 'heading' => 'What are you looking for?',
      'body' => 'Try a material like linen or oak, a room, or the name of a piece.',
      'actions' => [['Browse everything', route('shop'), true]],
  ]); ?>
<?php else: ?>
  <?php partial('partials/empty', [
      'icon' => 'search',
      'heading' => $filters['q'] !== '' ? 'Nothing matches “' . $filters['q'] . '” yet' : 'Nothing fits those filters',
      'body' => $filters['q'] !== ''
          ? 'Check the spelling, or try something broader like “vase” or “lamp”. Our range is small, so a simpler word usually works.'
          : 'Try widening the price range or switching off “in stock only”. New pieces arrive most weeks.',
      'actions' => [['Clear filters', strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: route('shop'), true], ['See what is new', url('/shop', ['sort' => 'newest'])]],
  ]); ?>
<?php endif; ?>
