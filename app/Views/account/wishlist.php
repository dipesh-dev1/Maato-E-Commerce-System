<?php partial('partials/account-shell-start', ['heading' => 'Saved for later']); ?>
<?php if ($products): ?>
  <div class="product-grid product-grid--3">
    <?php foreach ($products as $p): ?><?php partial('partials/product-card', ['p' => $p, 'wishlist' => $wishlist]); ?><?php endforeach; ?>
  </div>
<?php else: ?>
  <?php partial('partials/empty', [
      'icon' => 'heart', 'heading' => 'Nothing saved yet',
      'body' => 'Tap the heart on any piece to keep it here. Handy for comparing, or for dropping hints before a birthday.',
      'actions' => [['Browse the shop', route('shop'), true]],
  ]); ?>
<?php endif; ?>
<?php partial('partials/account-shell-end'); ?>
