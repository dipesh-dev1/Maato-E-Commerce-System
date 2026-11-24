<dialog class="drawer cart-drawer" id="cart-drawer" aria-labelledby="cart-drawer-title">
  <div class="dialog__head">
    <h2 class="dialog__title" id="cart-drawer-title">Your bag</h2>
    <button class="icon-btn" type="button" data-close-dialog aria-label="Close bag"><?= icon('close') ?></button>
  </div>
  <div class="cart-drawer__body" data-cart-drawer-body>
    <ul class="cart-drawer__lines" aria-hidden="true">
      <?php for ($i = 0; $i < 3; $i++): ?>
        <li class="line"><div class="skeleton skeleton--media"></div><div><div class="skeleton skeleton--line"></div><div class="skeleton skeleton--line"></div></div><div></div></li>
      <?php endfor; ?>
    </ul>
  </div>
</dialog>

<dialog class="modal quick-view" id="quick-view" aria-label="Quick view">
  <button class="icon-btn quick-view__close" type="button" data-close-dialog aria-label="Close quick view"><?= icon('close') ?></button>
  <div data-quick-view-body>
    <div class="quick-view__skeleton" aria-hidden="true">
      <div class="skeleton skeleton--media"></div>
      <div><div class="skeleton skeleton--line"></div><div class="skeleton skeleton--line"></div><div class="skeleton skeleton--line"></div></div>
    </div>
  </div>
</dialog>
