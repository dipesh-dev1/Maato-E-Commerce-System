<?php
use App\Core\Auth;
use App\Models\OrderRepository;
use App\Models\ReviewRepository;

$me = Auth::user();
$pendingOrders = can('orders.manage') ? ((new OrderRepository())->countByStatus()['pending'] ?? 0) : 0;
$pendingReviews = can('reviews.manage') ? (new ReviewRepository())->pendingCount() : 0;
$groups = [
    'Overview'  => [['/admin', 'Dashboard', 'grid', 'dashboard.view', 0]],
    'Catalogue' => [
        ['/admin/products', 'Products', 'box', 'products.manage', 0],
        ['/admin/categories', 'Categories', 'folder', 'catalog.manage', 0],
        ['/admin/brands', 'Makers & brands', 'badge', 'catalog.manage', 0],
    ],
    'Sales' => [
        ['/admin/orders', 'Orders', 'receipt', 'orders.manage', $pendingOrders],
        ['/admin/coupons', 'Coupons', 'tag', 'coupons.manage', 0],
        ['/admin/reviews', 'Reviews', 'message', 'reviews.manage', $pendingReviews],
    ],
    'People & settings' => [
        ['/admin/users', 'Users', 'users', 'users.manage', 0],
        ['/admin/settings', 'Store settings', 'settings', 'settings.manage', 0],
        ['/admin/activity', 'Activity log', 'activity', 'activity.view', 0],
    ],
];
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
<?php partial('partials/head', ['title' => ($title ?? 'Back office') . ' · Back office', 'styles' => ['css/admin.css']]); ?>
<meta name="robots" content="noindex">
<script type="module" src="<?= e(asset('js/admin/admin.js')) ?>"></script>
</head>
<body class="admin">
<a class="skip-link" href="#main">Skip to content</a>
<div class="admin-shell">
  <aside class="sidebar" id="sidebar" data-sidebar>
    <?php partial('partials/logo'); ?>
    <p class="sidebar__tag">Back office</p>
    <nav aria-label="Back office">
      <?php foreach ($groups as $group => $links): ?>
        <?php $visible = array_filter($links, static fn ($l) => can($l[3])); if (!$visible) continue; ?>
        <div class="sidebar__group">
          <p><?= e($group) ?></p>
          <ul>
            <?php foreach ($visible as [$path, $label, $ico, , $count]): ?>
              <li>
                <a class="sidebar__link" href="<?= e(url($path)) ?>"<?= ($path === '/admin' ? is_current($path) : is_current($path, true)) ? ' aria-current="page"' : '' ?>>
                  <?= icon($ico) ?> <?= e($label) ?>
                  <?php if ($count): ?><span class="sidebar__count" aria-label="<?= (int) $count ?> waiting"><?= (int) $count ?></span><?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar__foot">
      <div class="sidebar__user">
        <?php if ($me['avatar_path']): ?><img class="avatar" src="<?= e(media($me['avatar_path'])) ?>" alt=""><?php else: ?><span class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($me['name'], 0, 1))) ?></span><?php endif; ?>
        <span><?= e($me['name']) ?><small><?= e($me['role_name']) ?></small></span>
      </div>
      <a class="sidebar__link" href="<?= e(route('home')) ?>"><?= icon('external') ?> View store</a>
      <form method="post" action="<?= e(route('logout')) ?>"><?= csrf_field() ?><button class="sidebar__link" type="submit"><?= icon('logout') ?> Sign out</button></form>
    </div>
  </aside>
  <div>
    <header class="topbar">
      <div class="cluster">
        <button class="icon-btn menu-toggle" type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Open navigation"><?= icon('menu') ?></button>
        <nav class="topbar__crumbs" aria-label="Breadcrumb">
          <a href="<?= e(url('/admin')) ?>">Back office</a>
          <?php foreach ($crumbs ?? [] as [$label, $href]): ?>
            <?= icon('chevron-right', 'icon--sm') ?>
            <?php if ($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?>
          <?php endforeach; ?>
        </nav>
      </div>
      <span class="small muted"><?= e(date('l, j F')) ?></span>
    </header>
    <main class="page" id="main" tabindex="-1">
      <?= $content ?>
    </main>
  </div>
</div>
<?php partial('partials/toasts'); ?>
</body>
</html>
