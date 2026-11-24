<?php
/** Expects $result (page, pages) and $query (current query params). Renders nothing for a single page. */
if ($result['pages'] <= 1) {
    return;
}
$page = $result['page'];
$pages = $result['pages'];
$path = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
$link = static fn (int $n): string => $path . '?' . http_build_query(array_merge($query ?? [], ['page' => $n]));
$window = array_unique(array_filter([1, $page - 1, $page, $page + 1, $pages], static fn ($n) => $n >= 1 && $n <= $pages));
sort($window);
?>
<nav class="pagination" aria-label="Pagination">
  <?php if ($page > 1): ?>
    <a href="<?= e($link($page - 1)) ?>" rel="prev" data-page="<?= $page - 1 ?>"><?= icon('chevron-left', 'icon--sm') ?><span class="visually-hidden">Previous page</span></a>
  <?php else: ?>
    <span class="is-disabled" aria-hidden="true"><?= icon('chevron-left', 'icon--sm') ?></span>
  <?php endif; ?>
  <?php $prev = 0; foreach ($window as $n): ?>
    <?php if ($n - $prev > 1): ?><span class="gap">…</span><?php endif; ?>
    <?php if ($n === $page): ?>
      <span aria-current="page"><?= $n ?></span>
    <?php else: ?>
      <a href="<?= e($link($n)) ?>" data-page="<?= $n ?>" aria-label="Page <?= $n ?>"><?= $n ?></a>
    <?php endif; ?>
  <?php $prev = $n; endforeach; ?>
  <?php if ($page < $pages): ?>
    <a href="<?= e($link($page + 1)) ?>" rel="next" data-page="<?= $page + 1 ?>"><?= icon('chevron-right', 'icon--sm') ?><span class="visually-hidden">Next page</span></a>
  <?php else: ?>
    <span class="is-disabled" aria-hidden="true"><?= icon('chevron-right', 'icon--sm') ?></span>
  <?php endif; ?>
</nav>
