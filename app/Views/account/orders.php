<?php partial('partials/account-shell-start', ['heading' => 'Your orders']); ?>
<?php if ($result['items']): ?>
  <div class="order-rows">
    <?php foreach ($result['items'] as $o): ?><?php partial('account/partials/order-row', ['o' => $o]); ?><?php endforeach; ?>
  </div>
  <div class="listing__pagination"><?php partial('partials/pagination', ['result' => $result, 'query' => []]); ?></div>
<?php else: ?>
  <?php partial('partials/empty', [
      'icon' => 'receipt', 'heading' => 'No orders yet',
      'body' => "When you place an order, you'll be able to follow it here, from the workshop to your door.",
      'actions' => [['Start shopping', route('shop'), true]],
  ]); ?>
<?php endif; ?>
<?php partial('partials/account-shell-end'); ?>
