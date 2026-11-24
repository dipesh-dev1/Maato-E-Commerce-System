<?php
/** @var array $product @var array $images @var array $options @var array $reviews @var array $summary */
$crumbs = [['Shop', route('shop')]];
if ($parent) {
    $crumbs[] = [$parent['name'], route('category', ['slug' => $parent['slug']])];
}
$crumbs[] = [$product['category_name'], route('category', ['slug' => $product['category_slug']])];
$crumbs[] = [$product['name'], null];
$details = array_filter(array_map('trim', explode("\n", (string) $product['details'])));
?>
<?php partial('partials/breadcrumb', ['crumbs' => $crumbs]); ?>

<div class="container pdp">
  <div class="gallery" data-gallery>
    <?php if (count($images) > 1): ?>
      <ul class="gallery__thumbs" aria-label="Product images">
        <?php foreach ($images as $i => $img): ?>
          <li>
            <button class="gallery__thumb" type="button" data-gallery-thumb="<?= $i ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Show image <?= $i + 1 ?> of <?= count($images) ?>">
              <img src="<?= e(media($img['thumb_path'])) ?>" alt="" width="96" height="120" loading="lazy">
            </button>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <div>
      <div class="gallery__stage" data-gallery-stage tabindex="0" aria-label="Product images, scroll to see more">
        <?php foreach ($images ?: [['path' => null, 'alt' => $product['name']]] as $i => $img): ?>
          <img src="<?= e(media($img['path'])) ?>" alt="<?= e($img['alt'] ?: $product['name']) ?>" width="1000" height="1250" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
        <?php endforeach; ?>
      </div>
      <?php if (count($images) > 1): ?>
        <div class="gallery__dots" aria-hidden="true"><?php foreach ($images as $i => $img): ?><span<?= $i === 0 ? ' class="is-active"' : '' ?>></span><?php endforeach; ?></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="pdp__info">
    <div class="pdp__head">
      <p class="eyebrow"><?= e($product['brand_name'] ?? 'Maato') ?><?= $product['brand_origin'] ? ' · ' . e($product['brand_origin']) : '' ?></p>
      <h1 class="h1"><?= e($product['name']) ?></h1>
      <?php if ($summary['count'] > 0): ?>
        <a class="pdp__rating" href="#reviews"><?php partial('partials/stars', ['rating' => $summary['avg'], 'count' => $summary['count']]); ?></a>
      <?php endif; ?>
      <p class="pdp__short"><?= e($product['short_description']) ?></p>
    </div>

    <?php partial('store/partials/purchase-form', ['product' => $product, 'options' => $options, 'wishlist' => $wishlist]); ?>

    <ul class="perks">
      <li><?= icon('truck') ?><span>Valley delivery <?= e(money(setting('shipping_fee_valley', 150))) ?>, free over <?= e(money(setting('free_shipping_threshold', 10000))) ?>. Rest of Nepal <?= e(money(setting('shipping_fee_outside', 350))) ?>.</span></li>
      <li><?= icon('cash') ?><span>Pay in cash when it arrives. No card needed.</span></li>
      <?php if (setting('pickup_enabled') === '1'): ?><li><?= icon('store') ?><span>Or collect from the studio in Jhamsikhel and pay there.</span></li><?php endif; ?>
    </ul>

    <div class="accordion">
      <details open>
        <summary>About this piece <?= icon('plus') ?></summary>
        <div class="accordion__body prose">
          <?php foreach (preg_split('/\n\s*\n/', trim($product['description'])) as $para): ?><p><?= nl2br(e($para)) ?></p><?php endforeach; ?>
        </div>
      </details>
      <?php if ($details): ?>
        <details>
          <summary>Materials, size &amp; care <?= icon('plus') ?></summary>
          <div class="accordion__body"><ul class="spec-list"><?php foreach ($details as $line): ?><li><?= e($line) ?></li><?php endforeach; ?></ul></div>
        </details>
      <?php endif; ?>
      <details>
        <summary>Delivery &amp; returns <?= icon('plus') ?></summary>
        <div class="accordion__body prose">
          <p>Inside Kathmandu valley we deliver in 1–2 working days; elsewhere in Nepal, 3–5. Larger furniture is delivered by our own team and placed in the room you choose.</p>
          <p>If something isn't right, return it unused within 14 days and we'll refund you in cash or by bank transfer. <a href="<?= e(route('page', ['page' => 'shipping-returns'])) ?>">Full details</a>.</p>
        </div>
      </details>
    </div>
  </div>
</div>

<section class="section section--oat" id="reviews" aria-labelledby="reviews-title">
  <div class="container reviews">
    <div class="reviews__summary">
      <h2 class="h3" id="reviews-title">What people say</h2>
      <?php if ($summary['count'] > 0): ?>
        <div class="cluster">
          <span class="reviews__score"><?= e(number_format($summary['avg'], 1)) ?></span>
          <span><?php partial('partials/stars', ['rating' => $summary['avg']]); ?><span class="small muted">From <?= e(plural($summary['count'], 'review')) ?></span></span>
        </div>
        <ul class="bars" aria-label="Rating breakdown">
          <?php foreach ($summary['bars'] as $star => $n): $pct = $summary['count'] ? $n / $summary['count'] * 100 : 0; ?>
            <li>
              <span><?= $star ?> ★</span>
              <svg viewBox="0 0 100 6" preserveAspectRatio="none" aria-hidden="true"><rect class="track" width="100" height="6" rx="3"/><rect class="fill" width="<?= e(number_format($pct, 1, '.', '')) ?>" height="6" rx="3"/></svg>
              <span><?= $n ?><span class="visually-hidden"> reviews with <?= $star ?> stars</span></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="muted">No reviews yet. Reviews open to customers once their order has been delivered.</p>
      <?php endif; ?>
    </div>

    <div>
      <?php if ($reviews): ?>
        <div class="review-list">
          <?php foreach ($reviews as $r): ?>
            <article class="review">
              <?php partial('partials/stars', ['rating' => $r['rating']]); ?>
              <h3 class="review__title"><?= e($r['title']) ?></h3>
              <p class="review__body"><?= nl2br(e($r['body'])) ?></p>
              <p class="review__meta"><?= e(explode(' ', $r['author'])[0]) ?> · Verified purchase · <?= e(format_date($r['created_at'])) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($canReview): ?>
        <form class="review-form stack" method="post" action="<?= e(route('review.store', ['slug' => $product['slug']])) ?>">
          <?= csrf_field() ?>
          <h3 class="h4">You've had this a while. How is it?</h3>
          <fieldset class="rating-input">
            <legend>Your rating</legend>
            <?php for ($s = 5; $s >= 1; $s--): ?>
              <input type="radio" id="rate-<?= $s ?>" name="rating" value="<?= $s ?>"<?= (int) old('rating') === $s ? ' checked' : '' ?> required>
              <label for="rate-<?= $s ?>" title="<?= $s ?> star<?= $s > 1 ? 's' : '' ?>"><?= icon('star-fill') ?><span class="visually-hidden"><?= $s ?> star<?= $s > 1 ? 's' : '' ?></span></label>
            <?php endfor; ?>
          </fieldset>
          <?= field_error('rating') ?>
          <div class="field">
            <label class="label" for="review-title">Headline</label>
            <input class="input" id="review-title" name="title" maxlength="120" required value="<?= e(old('title')) ?>"<?= aria_invalid('title') ?>>
            <?= field_error('title') ?>
          </div>
          <div class="field">
            <label class="label" for="review-body">Your review</label>
            <textarea class="textarea" id="review-body" name="body" minlength="20" maxlength="2000" required<?= aria_invalid('body') ?>><?= e(old('body')) ?></textarea>
            <p class="hint">What do you use it for? How has it held up? At least 20 characters.</p>
            <?= field_error('body') ?>
          </div>
          <button class="btn" type="submit">Post review</button>
        </form>
      <?php elseif ($ownReview): ?>
        <p class="notice"><?= icon('check-circle') ?> <span>Thanks for your review<?= $ownReview['status'] === 'pending' ? '. We read every one before it goes up, usually within a day.' : '.' ?></span></p>
      <?php elseif (!auth_user()): ?>
        <p class="muted small"><a href="<?= e(route('login')) ?>">Sign in</a> to review pieces you've received.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section container" aria-labelledby="related-title">
  <div class="section-head"><h2 class="h2" id="related-title">Pairs well with</h2></div>
  <div class="product-grid scroller">
    <?php foreach ($related as $p): ?><?php partial('partials/product-card', ['p' => $p, 'wishlist' => $wishlist]); ?><?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($product['size_guide']): ?>
  <?php partial('store/partials/size-guide', ['type' => $product['size_guide']]); ?>
<?php endif; ?>
