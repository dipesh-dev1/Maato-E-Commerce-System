<?php
/**
 * Product card. Expects $p (card row from ProductRepository), $wishlist (ids).
 * Optional $variant: 'default' | 'feature' | 'row'. Optional $eager for above-the-fold images.
 */
$variant ??= 'default';
$url = route('product', ['slug' => $p['slug']]);
$soldOut = (int) $p['stock_total'] <= 0;
$saved = in_array((int) $p['id'], $wishlist ?? [], true);
$isNew = strtotime($p['created_at']) > strtotime('-21 days');
$off = $p['on_sale'] && (float) $p['regular_min'] > 0 ? (int) round((1 - $p['price_min'] / $p['regular_min']) * 100) : 0;
$loading = !empty($eager) ? 'eager' : 'lazy';
?>
<article class="card<?= $variant !== 'default' ? ' card--' . e($variant) : '' ?>"<?= $variant === 'default' ? ' data-reveal' : '' ?>>
  <div class="card__media">
    <img src="<?= e(media($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="<?= $loading ?>" width="480" height="600">
    <?php if ($p['image_alt'] && $variant !== 'row'): ?>
      <img class="card__img-alt" src="<?= e(media($p['image_alt'])) ?>" alt="" loading="lazy" width="480" height="600">
    <?php endif; ?>
    <?php if ($variant !== 'row'): ?>
      <div class="card__badges">
        <?php if ($soldOut): ?><span class="tag tag--soldout">Sold out</span>
        <?php elseif ($off > 0): ?><span class="tag tag--sale">−<?= $off ?>%</span><?php endif; ?>
        <?php if ($isNew && !$soldOut): ?><span class="tag">New</span><?php endif; ?>
      </div>
      <form class="card__wish" method="post" action="<?= e(route('wishlist.toggle', ['product' => $p['id']])) ?>" data-wishlist-form>
        <?= csrf_field() ?>
        <button class="wish-btn" type="submit" aria-pressed="<?= $saved ? 'true' : 'false' ?>" aria-label="Save <?= e($p['name']) ?> for later">
          <?= icon('heart', 'icon-off') ?><?= icon('heart-fill', 'icon-on') ?>
        </button>
      </form>
      <div class="card__quick">
        <button class="btn btn--sm" type="button" data-quick-view="<?= (int) $p['id'] ?>" aria-haspopup="dialog">
          <?= $soldOut ? 'Take a closer look' : 'Quick view' ?>
        </button>
      </div>
    <?php endif; ?>
  </div>
  <div class="card__body">
    <?php if ($variant === 'feature'): ?><p class="eyebrow">Studio pick · <?= e($p['category_name']) ?></p>
    <?php else: ?><p class="card__cat"><?= e($p['category_name']) ?></p><?php endif; ?>
    <h3 class="card__title"><a href="<?= e($url) ?>"><?= e($p['name']) ?></a></h3>
    <?php if ($variant === 'feature'): ?><p class="card__desc"><?= e($p['short_description']) ?></p><?php endif; ?>
    <div class="card__meta">
      <?php partial('partials/price', ['p' => $p]); ?>
      <?php if ((int) $p['rating_count'] > 0): ?>
        <?php partial('partials/stars', ['rating' => $p['rating_avg'], 'count' => $p['rating_count']]); ?>
      <?php endif; ?>
    </div>
  </div>
</article>
