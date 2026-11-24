<?php $logo = setting('logo_path'); ?>
<a class="logo" href="<?= e(route('home')) ?>" aria-label="<?= e(setting('store_name', 'Maato')) ?>, home">
  <?php if ($logo): ?>
    <img src="<?= e(media($logo)) ?>" alt="">
  <?php else: ?>
    <?= e(mb_strtolower(setting('store_name', 'Maato'))) ?><span class="logo__dot" aria-hidden="true"></span>
  <?php endif; ?>
</a>
