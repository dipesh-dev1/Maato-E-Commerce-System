<?php
$threshold = (float) setting('free_shipping_threshold', 0);
if ($threshold <= 0) {
    return;
}
$gap = $summary['free_shipping_gap'];
$pct = max(0, min(100, ($threshold - $gap) / $threshold * 100));
?>
<div class="ship-meter">
  <p><?= $gap > 0 ? 'Add <strong>' . e(money($gap)) . '</strong> more for free delivery in the valley.' : '<strong>Free valley delivery</strong> unlocked.' ?></p>
  <svg viewBox="0 0 100 4" preserveAspectRatio="none" aria-hidden="true"><rect class="track" width="100" height="4" rx="2"/><rect class="fill" width="<?= e(number_format($pct, 1, '.', '')) ?>" height="4" rx="2"/></svg>
</div>
