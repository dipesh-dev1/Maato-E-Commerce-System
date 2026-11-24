<!doctype html>
<html lang="en" class="no-js">
<head>
<?php partial('partials/head', get_defined_vars()); ?>
<script type="module" src="<?= e(asset('js/store.js')) ?>"></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="checkout-header">
  <div class="container checkout-header__inner">
    <?php partial('partials/logo'); ?>
    <a class="link-btn" href="<?= e(route('cart')) ?>"><?= icon('arrow-left', 'icon--sm') ?> Back to your bag</a>
  </div>
</header>
<main id="main" tabindex="-1">
<?= $content ?>
</main>
<?php partial('partials/toasts'); ?>
</body>
</html>
