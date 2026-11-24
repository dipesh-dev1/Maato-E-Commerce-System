<div class="container error-page">
  <div>
    <p class="error-page__code" aria-hidden="true"><?= (int) $status ?></p>
    <h1 class="h2"><?= e($message) ?></h1>
    <p class="lede">
      <?php if ($status === 419): ?>For your security, forms expire after a while. Go back, refresh the page and try once more.
      <?php elseif ($status === 403): ?>If you think you should have access, sign in with a different account or ask an administrator.
      <?php elseif ($status === 429): ?>We slow things down after several attempts to keep accounts safe.
      <?php else: ?>Try going back, or head to the shop.<?php endif; ?>
    </p>
    <div class="cluster">
      <a class="btn" href="<?= e(route('home')) ?>">Go to the homepage</a>
      <a class="btn btn--ghost" href="<?= e(route('page', ['page' => 'contact'])) ?>">Contact us</a>
    </div>
  </div>
  <img src="<?= e(asset('img/error-room.jpg')) ?>" alt="" width="900" height="1100" loading="lazy">
</div>
