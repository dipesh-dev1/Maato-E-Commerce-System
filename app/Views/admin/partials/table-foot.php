<?php /** Count + pagination under a data table. Expects $result, $filters, $noun. */ ?>
<div class="table-foot">
  <span data-table-count><?= e(plural((int) $result['total'], $noun, $nouns ?? null)) ?><?php if ($result['pages'] > 1): ?> · page <?= (int) $result['page'] ?> of <?= (int) $result['pages'] ?><?php endif; ?></span>
  <?php partial('partials/pagination', ['result' => $result, 'query' => array_filter($filters, static fn ($v) => $v !== null && $v !== '')]); ?>
</div>
