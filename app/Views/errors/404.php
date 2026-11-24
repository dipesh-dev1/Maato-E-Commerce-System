<div class="container error-page">
  <div>
    <p class="error-page__code" aria-hidden="true">404</p>
    <h1 class="h2">We couldn't find that page.</h1>
    <p class="lede">It may have moved, sold out for good, or the link might be mistyped. Try a search, or start from one of these.</p>
    <form action="<?= e(route('search')) ?>" method="get" role="search">
      <label class="visually-hidden" for="nf-q">Search products</label>
      <input class="input" id="nf-q" type="search" name="q" placeholder="Search the shop">
      <button class="btn" type="submit">Search</button>
    </form>
    <div class="cluster">
      <a class="chip" href="<?= e(route('shop')) ?>">Shop everything</a>
      <a class="chip" href="<?= e(url('/shop', ['sort' => 'newest'])) ?>">New in</a>
      <a class="chip" href="<?= e(route('page', ['page' => 'contact'])) ?>">Contact us</a>
    </div>
  </div>
  <img src="<?= e(asset('img/error-room.jpg')) ?>" alt="" width="900" height="1100" loading="lazy">
</div>
