<?php
/** Opens the account two-column layout. Expects $heading; closes in account-shell-end. */
$links = [
    ['/account', 'Overview', 'user'],
    ['/account/orders', 'Orders', 'receipt'],
    ['/account/addresses', 'Addresses', 'pin'],
    ['/account/wishlist', 'Saved for later', 'heart'],
];
?>
<?php partial('partials/breadcrumb', ['crumbs' => [['Your account', route('account')], [$heading, null]]]); ?>
<div class="container">
  <header class="account__head"><h1 class="h1"><?= e($heading) ?></h1></header>
  <div class="account">
    <nav class="account-nav" aria-label="Account">
      <ul>
        <?php foreach ($links as [$path, $label, $ico]): ?>
          <li><a href="<?= e(url($path)) ?>"<?= is_current($path) || ($path !== '/account' && is_current($path, true)) ? ' aria-current="page"' : '' ?>><?= icon($ico) ?> <?= e($label) ?></a></li>
        <?php endforeach; ?>
        <?php if (App\Core\Auth::isBackOffice()): ?>
          <li><a href="<?= e(url('/admin')) ?>"><?= icon('grid') ?> Back office</a></li>
        <?php endif; ?>
      </ul>
      <form method="post" action="<?= e(route('logout')) ?>">
        <?= csrf_field() ?>
        <button type="submit"><?= icon('logout') ?> Sign out</button>
      </form>
    </nav>
    <div>
