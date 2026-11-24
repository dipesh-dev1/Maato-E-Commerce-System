<?php
/** Address inputs. $a = existing values, $prefix keeps ids unique, $useOld = repopulate from failed submit. */
$val = static fn (string $k) => $useOld ? old($k, $a[$k] ?? '') : ($a[$k] ?? '');
$err = static fn (string $k) => $useOld ? field_error($k) : '';
$inv = static fn (string $k) => $useOld ? aria_invalid($k) : '';
?>
<div class="field-row">
  <div class="field">
    <label class="label" for="<?= $prefix ?>-label">Label</label>
    <input class="input" id="<?= $prefix ?>-label" name="label" maxlength="40" required placeholder="Home, Office, Parents" value="<?= e($val('label')) ?>"<?= $inv('label') ?>>
    <?= $err('label') ?>
  </div>
  <div class="field">
    <label class="label" for="<?= $prefix ?>-recipient">Recipient</label>
    <input class="input" id="<?= $prefix ?>-recipient" name="recipient_name" autocomplete="name" maxlength="120" required value="<?= e($val('recipient_name')) ?>"<?= $inv('recipient_name') ?>>
    <?= $err('recipient_name') ?>
  </div>
</div>
<div class="field">
  <label class="label" for="<?= $prefix ?>-phone">Phone</label>
  <input class="input" id="<?= $prefix ?>-phone" name="phone" type="tel" autocomplete="tel" required value="<?= e($val('phone')) ?>"<?= $inv('phone') ?>>
  <?= $err('phone') ?>
</div>
<div class="field">
  <label class="label" for="<?= $prefix ?>-line1">Street address</label>
  <input class="input" id="<?= $prefix ?>-line1" name="line1" autocomplete="address-line1" maxlength="160" required placeholder="House number, tole, marg" value="<?= e($val('line1')) ?>"<?= $inv('line1') ?>>
  <?= $err('line1') ?>
</div>
<div class="field">
  <label class="label" for="<?= $prefix ?>-line2">Landmark <span class="optional">(optional)</span></label>
  <input class="input" id="<?= $prefix ?>-line2" name="line2" autocomplete="address-line2" maxlength="160" placeholder="Near the temple, opposite the school" value="<?= e($val('line2')) ?>">
</div>
<div class="field-row">
  <div class="field">
    <label class="label" for="<?= $prefix ?>-city">City or town</label>
    <input class="input" id="<?= $prefix ?>-city" name="city" autocomplete="address-level2" maxlength="80" required value="<?= e($val('city')) ?>"<?= $inv('city') ?>>
    <?= $err('city') ?>
  </div>
  <div class="field">
    <label class="label" for="<?= $prefix ?>-district">District</label>
    <input class="input" id="<?= $prefix ?>-district" name="district" maxlength="80" required value="<?= e($val('district')) ?>"<?= $inv('district') ?>>
    <?= $err('district') ?>
  </div>
</div>
<div class="field-row">
  <div class="field">
    <label class="label" for="<?= $prefix ?>-province">Province</label>
    <select class="select" id="<?= $prefix ?>-province" name="province" required<?= $inv('province') ?>>
      <?php foreach ($provinces as $p): ?><option<?= $val('province') === $p || (!$val('province') && $p === 'Bagmati') ? ' selected' : '' ?>><?= e($p) ?></option><?php endforeach; ?>
    </select>
    <?= $err('province') ?>
  </div>
  <div class="field">
    <label class="label" for="<?= $prefix ?>-postal">Postal code <span class="optional">(optional)</span></label>
    <input class="input" id="<?= $prefix ?>-postal" name="postal_code" autocomplete="postal-code" maxlength="10" value="<?= e($val('postal_code')) ?>">
  </div>
</div>
