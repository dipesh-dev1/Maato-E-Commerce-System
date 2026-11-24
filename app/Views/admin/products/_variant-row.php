<?php
/** One editable variant row. $i is the row index (or a template placeholder), $row the values. */
$val = static fn (string $k) => (string) ($row[$k] ?? '');
$name = static fn (string $k) => "variants[{$i}][{$k}]";
// Show 14500.00 as 14500 so the figure fits its column; real decimals are kept.
$amount = static fn (string $k) => str_contains($val($k), '.') ? rtrim(rtrim($val($k), '0'), '.') : $val($k);
?>
<tr data-variant-row>
  <td><input type="hidden" name="<?= e($name('id')) ?>" value="<?= e($val('id')) ?>"><label class="visually-hidden" for="v<?= e((string) $i) ?>-sku">SKU</label><input class="input input--sku" id="v<?= e((string) $i) ?>-sku" name="<?= e($name('sku')) ?>" value="<?= e($val('sku')) ?>" required maxlength="64"></td>
  <td><label class="visually-hidden" for="v<?= e((string) $i) ?>-size">Size</label><input class="input" id="v<?= e((string) $i) ?>-size" name="<?= e($name('size')) ?>" value="<?= e($val('size')) ?>" maxlength="40" placeholder="—"></td>
  <td><label class="visually-hidden" for="v<?= e((string) $i) ?>-color">Colour</label><input class="input" id="v<?= e((string) $i) ?>-color" name="<?= e($name('color')) ?>" value="<?= e($val('color')) ?>" maxlength="40" placeholder="—"></td>
  <td><label class="visually-hidden" for="v<?= e((string) $i) ?>-hex">Swatch colour</label><input type="color" id="v<?= e((string) $i) ?>-hex" name="<?= e($name('color_hex')) ?>" value="<?= e($val('color_hex') ?: '#d9cfc1') ?>"></td>
  <td><label class="visually-hidden" for="v<?= e((string) $i) ?>-price">Price</label><input class="input input--num" id="v<?= e((string) $i) ?>-price" name="<?= e($name('price')) ?>" type="number" min="0" step="0.01" value="<?= e($amount('price')) ?>" placeholder="Default"></td>
  <td><label class="visually-hidden" for="v<?= e((string) $i) ?>-sale">Sale price</label><input class="input input--num" id="v<?= e((string) $i) ?>-sale" name="<?= e($name('sale_price')) ?>" type="number" min="0" step="0.01" value="<?= e($amount('sale_price')) ?>" placeholder="Default"></td>
  <td><label class="visually-hidden" for="v<?= e((string) $i) ?>-stock">Stock</label><input class="input input--num" id="v<?= e((string) $i) ?>-stock" name="<?= e($name('stock')) ?>" type="number" min="0" step="1" required value="<?= e($val('stock') !== '' ? $val('stock') : '0') ?>"></td>
  <td><button class="icon-btn" type="button" data-remove-variant aria-label="Remove this variant"><?= icon('trash') ?></button></td>
</tr>
