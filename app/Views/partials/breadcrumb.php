<?php /** Expects $crumbs: list of [label, url|null]. The last item is the current page. */ ?>
<nav class="breadcrumb container" aria-label="Breadcrumb">
  <ol>
    <li><a href="<?= e(route('home')) ?>">Home</a></li>
    <?php foreach ($crumbs as $i => [$label, $href]): ?>
      <li>
        <?php if ($href && $i < count($crumbs) - 1): ?>
          <a href="<?= e($href) ?>"><?= e($label) ?></a>
        <?php else: ?>
          <span aria-current="page"><?= e($label) ?></span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</nav>
