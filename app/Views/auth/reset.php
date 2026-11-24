<h1 class="h2">Choose a new password</h1>
<?php if (!$valid): ?>
  <p>This link has expired or has already been used. Reset links work once, for 30 minutes.</p>
  <a class="btn btn--lg" href="<?= e(route('password.forgot')) ?>">Send a new link</a>
<?php else: ?>
  <p>Pick something you haven't used here before.</p>
  <form method="post" action="<?= e(route('password.reset', ['token' => $token])) ?>" class="stack" novalidate>
    <?= csrf_field() ?>
    <div class="field">
      <label class="label" for="password">New password</label>
      <div class="password-field">
        <input class="input" id="password" type="password" name="password" autocomplete="new-password" required minlength="8"<?= aria_invalid('password') ?>>
        <button type="button" data-toggle-password aria-label="Show password" aria-controls="password"><?= icon('eye') ?></button>
      </div>
      <p class="hint">At least 8 characters, with a letter and a number.</p>
      <?= field_error('password') ?>
    </div>
    <div class="field">
      <label class="label" for="password_confirmation">Confirm new password</label>
      <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
    </div>
    <button class="btn btn--lg btn--block" type="submit">Save new password</button>
  </form>
<?php endif; ?>
