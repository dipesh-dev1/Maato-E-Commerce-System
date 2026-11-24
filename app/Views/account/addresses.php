<?php
$open = App\Core\Session::getFlash('address_form');
$returnTo ??= null;
?>
<?php partial('partials/account-shell-start', ['heading' => 'Addresses']); ?>
<?php if ($returnTo === 'checkout'): ?>
  <p class="notice"><?= icon('info') ?><span>Add your address below and we'll take you straight back to checkout.</span></p>
<?php endif; ?>
<div class="address-cards section--tight">
  <?php foreach ($addresses as $a): ?>
    <article class="address-card<?= $a['is_default'] ? ' is-default' : '' ?>">
      <div class="split"><strong><?= e($a['label']) ?></strong><?php if ($a['is_default']): ?><span class="badge badge--plain">Default</span><?php endif; ?></div>
      <address><?= e($a['recipient_name']) ?><br><?= e($a['line1']) ?><?= $a['line2'] ? '<br>' . e($a['line2']) : '' ?><br><?= e($a['city']) ?>, <?= e($a['district']) ?><br><?= e($a['province']) ?> <?= e((string) $a['postal_code']) ?><br><?= e($a['phone']) ?></address>
      <details<?= $open === (string) $a['id'] ? ' open' : '' ?>>
        <summary class="link-btn">Edit</summary>
        <form method="post" action="<?= e(route('account.addresses.update', ['id' => $a['id']])) ?>">
          <?= csrf_field() ?><?= method_field('PATCH') ?>
          <?php partial('account/partials/address-fields', ['a' => $a, 'prefix' => 'a' . $a['id'], 'provinces' => $provinces, 'useOld' => $open === (string) $a['id']]); ?>
          <p class="section--tight"><button class="btn btn--sm" type="submit">Save address</button></p>
        </form>
      </details>
      <div class="cluster">
        <?php if (!$a['is_default']): ?>
          <form method="post" action="<?= e(route('account.addresses.default', ['id' => $a['id']])) ?>"><?= csrf_field() ?><button class="link-btn" type="submit">Make default</button></form>
        <?php endif; ?>
        <form method="post" action="<?= e(route('account.addresses.destroy', ['id' => $a['id']])) ?>" data-confirm="Remove the “<?= e($a['label']) ?>” address? Past orders keep their original address.">
          <?= csrf_field() ?><?= method_field('DELETE') ?><button class="link-btn link-btn--danger" type="submit">Remove</button>
        </form>
      </div>
    </article>
  <?php endforeach; ?>

  <details class="address-card address-new" id="new-address"<?= $open === 'new' || !$addresses || $returnTo ? ' open' : '' ?>>
    <summary><?= icon('plus') ?> Add a new address</summary>
    <form method="post" action="<?= e(route('account.addresses.store')) ?>">
      <?= csrf_field() ?>
      <?php if ($returnTo): ?><input type="hidden" name="return" value="checkout"><?php endif; ?>
      <?php partial('account/partials/address-fields', ['a' => [], 'prefix' => 'new', 'provinces' => $provinces, 'useOld' => $open === 'new']); ?>
      <label class="check section--tight"><input type="checkbox" name="is_default" value="1"> Make this my default address</label>
      <p><button class="btn btn--sm" type="submit">Save address</button></p>
    </form>
  </details>
</div>
<?php partial('partials/account-shell-end'); ?>
