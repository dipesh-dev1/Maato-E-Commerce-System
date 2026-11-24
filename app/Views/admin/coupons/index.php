<div class="page-head">
  <div><h1 class="h1">Coupons</h1><p>Codes customers enter in their bag. Usage counts only completed checkouts; cancelled orders give the use back.</p></div>
  <button class="btn" type="button" data-new-record="coupon-form"><?= icon('plus', 'icon--sm') ?> New coupon</button>
</div>
<div class="a-card" data-table>
  <form class="toolbar" method="get" action="<?= e(route('admin.coupons')) ?>" data-table-filters>
    <?php partial('admin/partials/search', ['value' => $filters['q'], 'placeholder' => 'Search codes']); ?>
    <label class="visually-hidden" for="f-state">State</label>
    <select class="select" id="f-state" name="state">
      <option value="">All coupons</option>
      <option value="active"<?= $filters['state'] === 'active' ? ' selected' : '' ?>>Usable now</option>
      <option value="expired"<?= $filters['state'] === 'expired' ? ' selected' : '' ?>>Expired or used up</option>
    </select>
    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>"><input type="hidden" name="dir" value="<?= e($filters['dir']) ?>">
  </form>
  <div data-table-body><?php partial('admin/coupons/_table', get_defined_vars()); ?></div>
</div>

<dialog class="drawer slideover" id="coupon-form" aria-labelledby="coupon-form-title">
  <form method="post" data-record-form data-create="<?= e(route('admin.coupons.store')) ?>" data-update="<?= e(url('/admin/coupons/__ID__')) ?>">
    <?php partial('admin/partials/slideover-head', ['id' => 'coupon-form', 'noun' => 'coupon']); ?>
    <div class="dialog__body">
      <?= csrf_field() ?><input type="hidden" name="_method" value="POST" data-method-field>
      <div class="form-section">
        <div class="field"><label class="label" for="cp-code">Code</label><input class="input" id="cp-code" name="code" required maxlength="30" placeholder="DASHAIN10" autocomplete="off"><p class="hint">Letters, numbers and hyphens. Customers can type it in any case.</p><?php partial('admin/partials/field-error', ['name' => 'code']); ?></div>
        <div class="field"><label class="label" for="cp-desc">Internal note <span class="optional">(optional)</span></label><input class="input" id="cp-desc" name="description" maxlength="160"></div>
      </div>
      <div class="form-section">
        <h3>Discount</h3>
        <div class="field-row">
          <div class="field"><label class="label" for="cp-type">Type</label><select class="select" id="cp-type" name="type"><option value="percent">Percentage off</option><option value="fixed">Fixed amount off</option></select></div>
          <div class="field"><label class="label" for="cp-value">Value</label><input class="input" id="cp-value" name="value" type="number" min="0.01" step="0.01" required><?php partial('admin/partials/field-error', ['name' => 'value']); ?></div>
        </div>
        <div class="field"><label class="label" for="cp-min">Minimum bag value (<?= e(setting('currency_symbol', 'Rs.')) ?>)</label><input class="input" id="cp-min" name="min_cart_value" type="number" min="0" step="1" value="0"><?php partial('admin/partials/field-error', ['name' => 'min_cart_value']); ?></div>
      </div>
      <div class="form-section">
        <h3>Limits</h3>
        <div class="field"><label class="label" for="cp-limit">Total uses <span class="optional">(blank for unlimited)</span></label><input class="input" id="cp-limit" name="usage_limit" type="number" min="1" step="1"><?php partial('admin/partials/field-error', ['name' => 'usage_limit']); ?></div>
        <div class="field-row">
          <div class="field"><label class="label" for="cp-start">Starts</label><input class="input" id="cp-start" name="starts_at" type="datetime-local"><?php partial('admin/partials/field-error', ['name' => 'starts_at']); ?></div>
          <div class="field"><label class="label" for="cp-end">Expires</label><input class="input" id="cp-end" name="expires_at" type="datetime-local"><?php partial('admin/partials/field-error', ['name' => 'expires_at']); ?></div>
        </div>
        <div class="field"><label class="switch"><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
      </div>
    </div>
    <?php partial('admin/partials/slideover-foot'); ?>
  </form>
</dialog>
