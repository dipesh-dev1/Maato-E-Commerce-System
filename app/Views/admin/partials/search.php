<?php /** Search box for a table toolbar. Expects $value, $placeholder. */ ?>
<div class="search">
  <?= icon('search') ?>
  <label class="visually-hidden" for="table-q"><?= e($placeholder) ?></label>
  <input class="input" id="table-q" type="search" name="q" value="<?= e($value ?? '') ?>" placeholder="<?= e($placeholder) ?>" maxlength="80" autocomplete="off">
</div>
