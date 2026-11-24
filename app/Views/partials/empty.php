<?php /** Expects $icon, $heading, $body, optional $actions (list of [label, url, primary?]). */ ?>
<div class="empty">
  <span class="empty__icon"><?= icon($icon) ?></span>
  <h2 class="h3"><?= e($heading) ?></h2>
  <p><?= e($body) ?></p>
  <?php if (!empty($actions)): ?>
    <div class="cluster">
      <?php foreach ($actions as $a): ?>
        <a class="btn <?= !empty($a[2]) ? '' : 'btn--ghost' ?>" href="<?= e($a[1]) ?>"><?= e($a[0]) ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
