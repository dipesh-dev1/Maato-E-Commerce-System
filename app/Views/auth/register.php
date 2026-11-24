<h1 class="h2">Create an account</h1>
<p>Track orders, save addresses and keep a list of pieces you're thinking about.</p>
<form method="post" action="<?= e(route('register')) ?>" class="stack" novalidate>
  <?= csrf_field() ?>
  <div class="field">
    <label class="label" for="name">Full name</label>
    <input class="input" id="name" name="name" autocomplete="name" required maxlength="120" value="<?= e(old('name')) ?>"<?= aria_invalid('name') ?>>
    <?= field_error('name') ?>
  </div>
  <div class="field">
    <label class="label" for="email">Email</label>
    <input class="input" id="email" type="email" name="email" autocomplete="email" required value="<?= e(old('email')) ?>"<?= aria_invalid('email') ?>>
    <?= field_error('email') ?>
  </div>
  <div class="field">
    <label class="label" for="phone">Mobile number <span class="optional">(optional)</span></label>
    <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" placeholder="98XXXXXXXX" value="<?= e(old('phone')) ?>"<?= aria_invalid('phone') ?>>
    <p class="hint">Our delivery team calls ahead. We never send marketing texts.</p>
    <?= field_error('phone') ?>
  </div>
  <div class="field">
    <label class="label" for="password">Password</label>
    <div class="password-field">
      <input class="input" id="password" type="password" name="password" autocomplete="new-password" required minlength="8"<?= aria_invalid('password') ?>>
      <button type="button" data-toggle-password aria-label="Show password" aria-controls="password"><?= icon('eye') ?></button>
    </div>
    <p class="hint">At least 8 characters, with a letter and a number.</p>
    <?= field_error('password') ?>
  </div>
  <div class="field">
    <label class="label" for="password_confirmation">Confirm password</label>
    <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
  </div>
  <button class="btn btn--lg btn--block" type="submit">Create account</button>
</form>
<p class="auth__alt">Already have an account? <a href="<?= e(route('login')) ?>">Sign in</a>.</p>
