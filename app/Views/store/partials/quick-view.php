<?php $first = $images[0] ?? null; ?>
<div class="quick-view__grid">
  <div class="quick-view__media"><img src="<?= e(media($first['path'] ?? null)) ?>" alt="<?= e($product['name']) ?>" width="1000" height="1250"></div>
  <div class="quick-view__info">
    <h2 class="h3"><?= e($product['name']) ?></h2>
    <p class="muted"><?= e($product['short_description']) ?></p>
    <?php partial('store/partials/purchase-form', ['product' => $product, 'options' => $options, 'wishlist' => $wishlist, 'idPrefix' => 'qv']); ?>
    <a class="link-btn" href="<?= e(route('product', ['slug' => $product['slug']])) ?>">See full details <?= icon('arrow-right', 'icon--sm') ?></a>
  </div>
</div>
<?php if ($product['size_guide']): ?><?php partial('store/partials/size-guide', ['type' => $product['size_guide']]); ?><?php endif; ?>
