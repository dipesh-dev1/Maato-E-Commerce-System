<h1 class="h2">Welcome back</h1>
<p>Sign in to see your orders, saved pieces and addresses.</p>
<form method="post" action="<?= e(route('login')) ?>" class="stack" novalidate>
  <?= csrf_field() ?>
  <?php if ($formError = error_for('form')): ?>
    <p class="form-error" role="alert"><?= icon('alert') ?> <?= e($formError) ?></p>
  <?php endif; ?>
  <div class="field">
    <label class="label" for="email">Email</label>
    <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email')) ?>"<?= aria_invalid('email') ?>>
    <?= field_error('email') ?>
  </div>
  <div class="field">
    <div class="split"><label class="label" for="password">Password</label><a class="small" href="<?= e(route('password.forgot')) ?>">Forgot it?</a></div>
    <div class="password-field">
      <input class="input" id="password" type="password" name="password" autocomplete="current-password" required<?= aria_invalid('password') ?>>
      <button type="button" data-toggle-password aria-label="Show password" aria-controls="password"><?= icon('eye') ?></button>
    </div>
    <?= field_error('password') ?>
  </div>
  <button class="btn btn--lg btn--block" type="submit">Sign in</button>
</form>
<p class="auth__alt">New to Maato? <a href="<?= e(route('register')) ?>">Create an account</a>. It takes a minute and keeps your bag between visits.</p>
