<?php /** One bag line. Expects $line; $size = 'sm' for the drawer. */ ?>
<li class="line" data-cart-line="<?= (int) $line['variant_id'] ?>">
  <a class="line__img" href="<?= e(route('product', ['slug' => $line['slug']])) ?>" tabindex="-1" aria-hidden="true">
    <img src="<?= e(media($line['image'])) ?>" alt="" width="96" height="120" loading="lazy">
  </a>
  <div>
    <p class="line__name"><a href="<?= e(route('product', ['slug' => $line['slug']])) ?>"><?= e($line['name']) ?></a></p>
    <?php if ($line['label']): ?><p class="line__variant"><?= e($line['label']) ?></p><?php endif; ?>
    <div class="line__controls">
      <form method="post" action="<?= e(route('cart.update', ['variant' => $line['variant_id']])) ?>" data-cart-update>
        <?= csrf_field() ?><?= method_field('PATCH') ?>
        <div class="qty qty--sm" data-qty>
          <button type="button" data-qty-step="-1" aria-label="One fewer <?= e($line['name']) ?>"><?= icon('minus', 'icon--sm') ?></button>
          <label class="visually-hidden" for="qty-<?= e(($size ?? 'page') . $line['variant_id']) ?>">Quantity of <?= e($line['name']) ?></label>
          <input id="qty-<?= e(($size ?? 'page') . $line['variant_id']) ?>" type="number" name="quantity" value="<?= (int) $line['quantity'] ?>" min="0" max="<?= max(1, min(10, $line['stock'])) ?>" inputmode="numeric">
          <button type="button" data-qty-step="1" aria-label="One more <?= e($line['name']) ?>"><?= icon('plus', 'icon--sm') ?></button>
        </div>
        <noscript><button class="link-btn" type="submit">Update</button></noscript>
      </form>
      <form method="post" action="<?= e(route('cart.remove', ['variant' => $line['variant_id']])) ?>" data-cart-remove>
        <?= csrf_field() ?><?= method_field('DELETE') ?>
        <button class="remove-btn" type="submit">Remove<span class="visually-hidden"> <?= e($line['name']) ?></span></button>
      </form>
    </div>
    <?php if ($line['problem']): ?><p class="line__problem"><?= e($line['problem']) ?></p><?php endif; ?>
  </div>
  <p class="line__price">
    <?= e(money($line['line_total'])) ?>
    <?php if ($line['on_sale']): ?><s><?= e(money($line['regular'] * $line['quantity'])) ?></s><?php endif; ?>
  </p>
</li>
