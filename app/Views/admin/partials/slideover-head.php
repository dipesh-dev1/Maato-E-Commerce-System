<?php /** Header of a record slide-over. Expects $id, $noun. */ ?>
<div class="dialog__head">
  <h2 class="dialog__title" id="<?= e($id) ?>-title" data-slideover-title data-new="New <?= e($noun) ?>" data-edit="Edit <?= e($noun) ?>">New <?= e($noun) ?></h2>
  <button class="icon-btn" type="button" data-close-dialog aria-label="Close"><?= icon('close') ?></button>
</div>
