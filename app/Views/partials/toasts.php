<?php $toast = App\Core\Session::getFlash('toast'); ?>
<div class="toasts" data-toasts role="status" aria-live="polite">
  <?php if ($toast): ?>
    <div class="toast toast--<?= e($toast['type']) ?>" data-toast>
      <?= icon(['success' => 'check-circle', 'error' => 'alert', 'info' => 'info'][$toast['type']] ?? 'info') ?>
      <p><?= e($toast['message']) ?></p>
      <button class="toast__close" type="button" data-toast-close aria-label="Dismiss"><?= icon('close', 'icon--sm') ?></button>
    </div>
  <?php endif; ?>
</div>
<template id="toast-template">
  <div class="toast" data-toast>
    <svg class="icon" aria-hidden="true"><use href=""></use></svg>
    <p></p>
    <button class="toast__close" type="button" data-toast-close aria-label="Dismiss"><?= icon('close', 'icon--sm') ?></button>
  </div>
</template>
<dialog class="modal" id="confirm-dialog" aria-labelledby="confirm-title">
  <form method="dialog">
    <div class="dialog__body stack">
      <h2 class="dialog__title" id="confirm-title">Are you sure?</h2>
      <p class="muted" data-confirm-message></p>
    </div>
    <div class="dialog__foot">
      <button class="btn btn--ghost" value="cancel">Keep it</button>
      <button class="btn btn--danger" value="confirm" data-confirm-ok>Yes, continue</button>
    </div>
  </form>
</dialog>
