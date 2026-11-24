<?php $sent = App\Core\Session::getFlash('sent'); ?>
<h1 class="h2">Reset your password</h1>
<?php if ($sent): ?>
  <p>If there's an account for <strong><?= e($sent) ?></strong>, a reset link is on its way. It works for 30 minutes.</p>
  <div class="notice"><?= icon('mail') ?><span>Can't see it? Check your spam folder, or <a href="<?= e(route('password.forgot')) ?>">try a different email</a>.</span></div>
<?php else: ?>
  <p>Enter the email you shop with and we'll send you a link to choose a new password.</p>
  <form method="post" action="<?= e(route('password.forgot')) ?>" class="stack" novalidate>
    <?= csrf_field() ?>
    <?php if ($formError = error_for('form')): ?><p class="form-error" role="alert"><?= icon('alert') ?> <?= e($formError) ?></p><?php endif; ?>
    <div class="field">
      <label class="label" for="email">Email</label>
      <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email')) ?>"<?= aria_invalid('email') ?>>
      <?= field_error('email') ?>
    </div>
    <button class="btn btn--lg btn--block" type="submit">Send reset link</button>
  </form>
<?php endif; ?>
<p class="auth__alt">Remembered it? <a href="<?= e(route('login')) ?>">Back to sign in</a>.</p>
