<?php
/** @var array $featured @var array $newest @var array $onSale @var array $categories @var array $wishlist */
$lead = $featured[0] ?? null;
$rest = array_slice($featured, 1, 6);
?>
<section class="hero container" aria-labelledby="hero-title">
  <div class="hero__grid">
    <div class="hero__copy">
      <p class="eyebrow">The autumn edit · Thimi, Patan, Gorkha</p>
      <h1 class="display" id="hero-title">Made slowly, <em>kept</em> for years.</h1>
      <p class="lede">Stoneware, linen, cane and solid oak from seven small workshops across Nepal. Delivered to your door, and paid for when it arrives.</p>
      <div class="hero__actions">
        <a class="btn btn--lg" href="<?= e(route('shop')) ?>">Shop the edit</a>
        <a class="link-btn" href="<?= e(route('page', ['page' => 'our-story'])) ?>">Meet the makers <?= icon('arrow-right', 'icon--sm') ?></a>
      </div>
    </div>
    <div class="hero__main">
      <img src="<?= e(asset('img/journal-room.jpg')) ?>" alt="A calm living room with a linen sofa, low wooden tables and pale walls" width="1400" height="1650" fetchpriority="high">
      <span class="hero__note">Photographed at the Jhamsikhel studio</span>
      <figure class="hero__inset">
        <a href="<?= e(route('product', ['slug' => 'dhunge-speckled-tumblers-set-of-4'])) ?>">
          <img src="<?= e(asset('img/hero-inset.jpg')) ?>" alt="Speckled stoneware tumblers stacked on a white table" width="600" height="720" loading="lazy">
        </a>
        <figcaption><strong>Dhunge tumblers</strong>Glazed in Thimi · <?= e(money(2800)) ?> for four</figcaption>
      </figure>
    </div>
  </div>
</section>

<section class="values" aria-label="Why shop with us">
  <ul class="container values__list">
    <li><?= icon('cash', 'icon--lg') ?><p><strong>Pay when it arrives</strong><span>Cash on delivery anywhere in Nepal, or pay when you collect from the studio.</span></p></li>
    <li><?= icon('truck', 'icon--lg') ?><p><strong>Free valley delivery over <?= e(money(setting('free_shipping_threshold', 10000))) ?></strong><span>Kathmandu, Lalitpur and Bhaktapur in 1–2 days. Elsewhere in 3–5.</span></p></li>
    <li><?= icon('hand', 'icon--lg') ?><p><strong>Seven workshops, known by name</strong><span>Every piece says who made it and where. We visit each one every season.</span></p></li>
  </ul>
</section>

<section class="section container" aria-labelledby="rooms-title">
  <div class="section-head">
    <h2 class="h2" id="rooms-title">Five things we care about getting right</h2>
    <p>We keep the range small on purpose. If it's here, we've lived with it first.</p>
  </div>
  <div class="bento">
    <?php foreach (array_values($categories) as $i => $cat): ?>
      <a class="bento__tile" href="<?= e(route('category', ['slug' => $cat['slug']])) ?>" data-reveal>
        <img src="<?= e(media($cat['image_path'])) ?>" alt="" loading="lazy" width="900" height="1100">
        <span class="bento__label">
          <span><strong><?= e($cat['name']) ?></strong><?php if ($i === 0 && $cat['tagline']): ?><small><?= e($cat['tagline']) ?></small><?php endif; ?></span>
          <?= icon('arrow-right') ?>
        </span>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($lead): ?>
<section class="section section--oat" aria-labelledby="edit-title">
  <div class="container">
    <div class="section-head">
      <h2 class="h2" id="edit-title">The studio's favourites this month</h2>
      <a class="link-btn" href="<?= e(route('shop')) ?>">Shop everything <?= icon('arrow-right', 'icon--sm') ?></a>
    </div>
    <div class="product-grid">
      <?php partial('partials/product-card', ['p' => $lead, 'wishlist' => $wishlist, 'variant' => 'feature']); ?>
      <?php foreach ($rest as $p): ?>
        <?php partial('partials/product-card', ['p' => $p, 'wishlist' => $wishlist]); ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section container" aria-labelledby="story-title">
  <div class="story">
    <figure class="story__media" data-reveal>
      <img src="<?= e(asset('img/story-studio.jpg')) ?>" alt="A wooden shelf with ceramics, books and a small plant in the Maato studio" loading="lazy" width="1000" height="1250">
      <figcaption>The sample shelf, Jhamsikhel</figcaption>
    </figure>
    <div class="story__copy">
      <p class="eyebrow">How we work</p>
      <h2 class="h1" id="story-title">Eleven potters, four wheels, one wood-fired kiln.</h2>
      <p class="lede">Most of our ceramics come from one family in Thimi. We design a shape, they throw a dozen, and we live with them for a month before deciding. Some never make it. The ones that do are made in small runs, so they rarely look identical, and that's the point.</p>
      <dl class="story__facts">
        <div><dt>Workshops</dt><dd>7</dd></div>
        <div><dt>Typical batch</dt><dd>24 pieces</dd></div>
        <div><dt>Average piece travels</dt><dd>38 km</dd></div>
      </dl>
      <a class="btn btn--ghost" href="<?= e(route('page', ['page' => 'our-story'])) ?>">Read our story</a>
    </div>
  </div>
</section>



<?php if ($onSale): ?>
<section class="section--tight container" aria-labelledby="sale-title">
  <div class="section-head">
    <h2 class="h3" id="sale-title">Quietly reduced</h2>
    <a class="link-btn" href="<?= e(url('/shop', ['on_sale' => 1])) ?>">All reduced pieces <?= icon('arrow-right', 'icon--sm') ?></a>
  </div>
  <div class="row-cards">
    <?php foreach ($onSale as $p): ?>
      <?php partial('partials/product-card', ['p' => $p, 'wishlist' => $wishlist, 'variant' => 'row']); ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="section container" aria-labelledby="new-title">
  <div class="section-head">
    <h2 class="h2" id="new-title">Just in from the workshops</h2>
    <a class="link-btn" href="<?= e(url('/shop', ['sort' => 'newest'])) ?>">See all new pieces <?= icon('arrow-right', 'icon--sm') ?></a>
  </div>
  <div class="product-grid scroller">
    <?php foreach ($newest as $p): ?>
      <?php partial('partials/product-card', ['p' => $p, 'wishlist' => $wishlist]); ?>
    <?php endforeach; ?>
  </div>
</section>

<section class="callout" aria-labelledby="delivery-title">
  <img src="<?= e(asset('img/journal-room.jpg')) ?>" alt="" loading="lazy" width="1600" height="900">
  <div class="callout__panel" data-reveal>
    <p class="eyebrow">Delivery &amp; payment</p>
    <h2 class="h3" id="delivery-title">No cards, no fuss. Pay when it's in your hands.</h2>
    <ul>
      <li><?= icon('truck') ?> <span>Valley delivery <?= e(money(setting('shipping_fee_valley', 150))) ?>, rest of Nepal <?= e(money(setting('shipping_fee_outside', 350))) ?>. Free over <?= e(money(setting('free_shipping_threshold', 10000))) ?>.</span></li>
      <li><?= icon('store') ?> <span>Or collect from the studio in Jhamsikhel and pay there.</span></li>
      <li><?= icon('refresh') ?> <span>Changed your mind? Return unused pieces within 14 days.</span></li>
    </ul>
    <a class="link-btn" href="<?= e(route('page', ['page' => 'shipping-returns'])) ?>">Delivery &amp; returns in full <?= icon('arrow-right', 'icon--sm') ?></a>
  </div>
</section>
