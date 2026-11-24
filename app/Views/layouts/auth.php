<!doctype html>
<html lang="en" class="no-js">
<head>
<?php partial('partials/head', get_defined_vars()); ?>
<script type="module" src="<?= e(asset('js/store.js')) ?>"></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="auth">
  <div class="auth__media" aria-hidden="true">
    <img src="<?= e(asset('img/auth-side.jpg')) ?>" alt="">
    <blockquote class="auth__quote">
      <p>“I order the linen every autumn. It arrives tied with twine and smells faintly of the loom.”</p>
      <cite>Aarati, Lalitpur, customer since 2023</cite>
    </blockquote>
  </div>
  <div class="auth__panel">
    <?php partial('partials/logo'); ?>
    <main id="main" class="auth__form" tabindex="-1">
      <?= $content ?>
    </main>
  </div>
</div>
<?php partial('partials/toasts'); ?>
</body>
</html>
