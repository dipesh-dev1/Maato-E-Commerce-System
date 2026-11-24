<?php /** Expects $rating (float) and optional $count. */ ?>
<span class="stars" role="img" aria-label="Rated <?= e(number_format((float) $rating, 1)) ?> out of 5<?= isset($count) ? ', ' . e(plural((int) $count, 'review')) : '' ?>">
  <?php for ($i = 1; $i <= 5; $i++): ?>
    <?= icon($i <= round((float) $rating) ? 'star-fill' : 'star', $i <= round((float) $rating) ? '' : 'is-empty') ?>
  <?php endfor; ?>
  <?php if (isset($count)): ?><span class="stars__count" aria-hidden="true">(<?= (int) $count ?>)</span><?php endif; ?>
</span>
