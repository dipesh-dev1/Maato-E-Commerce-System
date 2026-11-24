<?php
/** Expects $navTree (category tree), $cartCount. */
$user = auth_user();
?>
<div class="announce">
  Free delivery inside the valley on orders over <?= e(money(setting('free_shipping_threshold', 10000))) ?> · Cash on delivery across Nepal
</div>
<header class="site-header" data-header>
  <div class="container site-header__inner">
    <div class="cluster">
      <button class="icon-btn menu-toggle" type="button" data-open-dialog="mobile-nav" aria-label="Open menu" aria-haspopup="dialog"><?= icon('menu') ?></button>
      <nav class="primary-nav" aria-label="Main">
        <button class="nav-link" type="button" aria-expanded="false" aria-controls="mega-shop" data-mega-trigger>Shop <?= icon('chevron-down') ?></button>
        <a class="nav-link" href="<?= e(url('/shop', ['sort' => 'newest'])) ?>">New in</a>
        <a class="nav-link" href="<?= e(url('/shop', ['on_sale' => 1])) ?>">Sale</a>
        <a class="nav-link" href="<?= e(route('page', ['page' => 'our-story'])) ?>"<?= is_current('/pages/our-story') ? ' aria-current="page"' : '' ?>>Our story</a>
      </nav>
    </div>

    <?php partial('partials/logo'); ?>

    <div class="header-actions">
      <button class="icon-btn" type="button" data-open-search aria-label="Search" aria-haspopup="dialog"><?= icon('search') ?></button>
      <a class="icon-btn hide-mobile" href="<?= e($user ? route('account') : route('login')) ?>" aria-label="<?= $user ? 'Your account' : 'Sign in' ?>"><?= icon('user') ?></a>
      <a class="icon-btn hide-mobile" href="<?= e(route('account.wishlist')) ?>" aria-label="Saved for later"><?= icon('heart') ?></a>
      <a class="icon-btn" href="<?= e(route('cart')) ?>" data-open-cart aria-label="Bag, <?= (int) $cartCount ?> items" aria-haspopup="dialog">
        <?= icon('bag') ?>
        <span class="count-bubble" data-cart-count<?= $cartCount ? '' : ' hidden' ?>><?= (int) $cartCount ?></span>
      </a>
    </div>
  </div>

  <div class="mega" id="mega-shop" hidden data-mega>
    <div class="container mega__inner">
      <?php foreach ($navTree as $cat): ?>
        <div class="mega__col">
          <h3><a href="<?= e(route('category', ['slug' => $cat['slug']])) ?>"><?= e($cat['name']) ?></a></h3>
          <?php if ($cat['tagline']): ?><p><?= e($cat['tagline']) ?></p><?php endif; ?>
          <ul>
            <?php foreach ($cat['children'] as $child): ?>
              <li><a href="<?= e(route('category', ['slug' => $child['slug']])) ?>"><?= e($child['name']) ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?= e(route('category', ['slug' => $cat['slug']])) ?>">Shop all <?= e(mb_strtolower($cat['name'])) ?></a></li>
          </ul>
        </div>
      <?php endforeach; ?>
      <a class="mega__feature" href="<?= e(url('/shop', ['sort' => 'newest'])) ?>">
        <img src="<?= e(asset('img/journal-room.jpg')) ?>" alt="" loading="lazy" width="320" height="400">
        <span class="stack">
          <span class="eyebrow">New this month</span>
          <span class="h4">Cane, cedar and the first of the winter linen</span>
          <span class="link-btn">See what's new <?= icon('arrow-right', 'icon--sm') ?></span>
        </span>
      </a>
    </div>
  </div>
</header>

<dialog class="drawer drawer--left mobile-nav" id="mobile-nav" aria-label="Menu">
  <div class="dialog__head">
    <?php partial('partials/logo'); ?>
    <button class="icon-btn" type="button" data-close-dialog aria-label="Close menu"><?= icon('close') ?></button>
  </div>
  <div class="dialog__body">
    <ul class="mobile-nav__list">
      <?php foreach ($navTree as $cat): ?>
        <li>
          <?php if ($cat['children']): ?>
            <details>
              <summary><?= e($cat['name']) ?> <?= icon('chevron-down') ?></summary>
              <ul>
                <?php foreach ($cat['children'] as $child): ?>
                  <li><a href="<?= e(route('category', ['slug' => $child['slug']])) ?>"><?= e($child['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?= e(route('category', ['slug' => $cat['slug']])) ?>">Shop all</a></li>
              </ul>
            </details>
          <?php else: ?>
            <a href="<?= e(route('category', ['slug' => $cat['slug']])) ?>"><?= e($cat['name']) ?></a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      <li><a href="<?= e(url('/shop', ['sort' => 'newest'])) ?>">New in</a></li>
      <li><a href="<?= e(url('/shop', ['on_sale' => 1])) ?>">Sale</a></li>
      <li><a href="<?= e(route('page', ['page' => 'our-story'])) ?>">Our story</a></li>
    </ul>
    <div class="mobile-nav__meta">
      <a href="<?= e($user ? route('account') : route('login')) ?>"><?= icon('user') ?> <?= $user ? 'Your account' : 'Sign in or create an account' ?></a>
      <a href="<?= e(route('account.wishlist')) ?>"><?= icon('heart') ?> Saved for later</a>
      <a href="<?= e(route('page', ['page' => 'contact'])) ?>"><?= icon('phone') ?> <?= e(setting('support_phone', '')) ?></a>
    </div>
  </div>
</dialog>

<dialog class="modal search-dialog" id="search-dialog" aria-label="Search the shop">
  <form class="search-dialog__form" action="<?= e(route('search')) ?>" method="get" role="search" data-search-form>
    <?= icon('search', 'icon--lg') ?>
    <label class="visually-hidden" for="search-input">Search products</label>
    <input class="search-dialog__input" id="search-input" type="search" name="q" placeholder="Search mugs, linen, lamps…" autocomplete="off"
           role="combobox" aria-expanded="false" aria-controls="search-results" aria-autocomplete="list" data-search-input maxlength="80">
    <button class="icon-btn" type="button" data-close-dialog aria-label="Close search"><?= icon('close') ?></button>
  </form>
  <div class="search-results" id="search-results" role="listbox" aria-label="Suggestions" data-search-results>
    <div class="search-results__hint">
      <p>Popular right now</p>
      <ul>
        <?php foreach (['Mug', 'Linen', 'Pendant', 'Candle', 'Rug'] as $term): ?>
          <li><a class="chip" href="<?= e(url('/search', ['q' => $term])) ?>"><?= e($term) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</dialog>
