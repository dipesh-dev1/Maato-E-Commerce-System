<?php /** Expects $type: bedding | rug | furniture */ ?>
<dialog class="drawer" id="size-guide" aria-labelledby="size-guide-title">
  <div class="dialog__head">
    <h2 class="dialog__title" id="size-guide-title">Size guide</h2>
    <button class="icon-btn" type="button" data-close-dialog aria-label="Close size guide"><?= icon('close') ?></button>
  </div>
  <div class="dialog__body">
    <?php partial('store/partials/size-tables', ['type' => $type]); ?>
  </div>
</dialog>
