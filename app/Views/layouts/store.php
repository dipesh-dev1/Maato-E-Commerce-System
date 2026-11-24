<?php
$navTree = (new App\Models\CategoryRepository())->tree();
$cartCount = (new App\Services\CartService())->count();
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
<?php partial('partials/head', get_defined_vars()); ?>
<script type="module" src="<?= e(asset('js/store.js')) ?>"></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<?php partial('partials/header', ['navTree' => $navTree, 'cartCount' => $cartCount]); ?>
<main id="main" tabindex="-1">
<?= $content ?>
</main>
<?php partial('partials/footer', ['navTree' => $navTree]); ?>
<?php partial('partials/store-dialogs'); ?>
<?php partial('partials/toasts'); ?>
</body>
</html>
