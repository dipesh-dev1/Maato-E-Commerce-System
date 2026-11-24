<?php partial('partials/account-shell-start', ['heading' => 'Hello, ' . explode(' ', $user['name'])[0]]); ?>

<section class="panel" id="recent" aria-labelledby="recent-title">
  <div class="panel__head">
    <h2 id="recent-title">Recent orders</h2>
    <?php if ($recent): ?><a class="link-btn" href="<?= e(route('account.orders')) ?>">All orders</a><?php endif; ?>
  </div>
  <?php if ($recent): ?>
    <div class="order-rows">
      <?php foreach (array_slice($recent, 0, 3) as $o): ?><?php partial('account/partials/order-row', ['o' => $o]); ?><?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="muted">No orders yet. When you place one, you can follow it from here, from the studio to your door.</p>
  <?php endif; ?>
</section>

<section class="panel" id="profile" aria-labelledby="profile-title">
  <div class="panel__head"><h2 id="profile-title">Profile</h2></div>
  <div class="avatar-row">
    <?php if ($user['avatar_path']): ?>
      <img class="avatar" src="<?= e(media($user['avatar_path'])) ?>" alt="Your profile photo" width="72" height="72">
    <?php else: ?>
      <span class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
    <?php endif; ?>
    <form method="post" action="<?= e(route('account.avatar')) ?>" enctype="multipart/form-data" class="stack stack--tight">
      <?= csrf_field() ?>
      <label class="label" for="avatar">Profile photo</label>
      <div class="cluster">
        <input class="file-input" id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required<?= aria_invalid('avatar') ?>>
        <button class="btn btn--ghost btn--sm" type="submit">Upload</button>
      </div>
      <p class="hint">JPG, PNG or WebP, up to <?= (int) (config('app.uploads.max_bytes') / 1048576) ?> MB.</p>
      <?= field_error('avatar') ?>
    </form>
    <?php if ($user['avatar_path']): ?>
      <form method="post" action="<?= e(route('account.avatar.remove')) ?>" data-confirm="Remove your profile photo?">
        <?= csrf_field() ?><?= method_field('DELETE') ?>
        <button class="link-btn link-btn--danger" type="submit">Remove photo</button>
      </form>
    <?php endif; ?>
  </div>
  <form method="post" action="<?= e(route('account.profile')) ?>">
    <?= csrf_field() ?>
    <div class="field-row">
      <div class="field">
        <label class="label" for="name">Full name</label>
        <input class="input" id="name" name="name" autocomplete="name" required maxlength="120" value="<?= e(old('name', $user['name'])) ?>"<?= aria_invalid('name') ?>>
        <?= field_error('name') ?>
      </div>
      <div class="field">
        <label class="label" for="phone">Mobile number</label>
        <input class="input" id="phone" type="tel" name="phone" autocomplete="tel" value="<?= e(old('phone', $user['phone'] ?? '')) ?>"<?= aria_invalid('phone') ?>>
        <?= field_error('phone') ?>
      </div>
    </div>
    <div class="field">
      <label class="label" for="email-ro">Email</label>
      <input class="input" id="email-ro" type="email" value="<?= e($user['email']) ?>" readonly aria-describedby="email-hint">
      <p class="hint" id="email-hint">To change your email, contact us. It's how we confirm your identity.</p>
    </div>
    <p><button class="btn" type="submit">Save profile</button></p>
  </form>
</section>

<section class="panel" id="security" aria-labelledby="security-title">
  <div class="panel__head"><h2 id="security-title">Password</h2></div>
  <form method="post" action="<?= e(route('account.password')) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="field">
      <label class="label" for="current_password">Current password</label>
      <input class="input" id="current_password" type="password" name="current_password" autocomplete="current-password" required<?= aria_invalid('current_password') ?>>
      <?= field_error('current_password') ?>
    </div>
    <div class="field-row">
      <div class="field">
        <label class="label" for="new_password">New password</label>
        <input class="input" id="new_password" type="password" name="password" autocomplete="new-password" required minlength="8"<?= aria_invalid('password') ?>>
        <?= field_error('password') ?>
      </div>
      <div class="field">
        <label class="label" for="password_confirmation">Confirm new password</label>
        <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
      </div>
    </div>
    <p class="hint">At least 8 characters, with a letter and a number.</p>
    <p><button class="btn" type="submit">Change password</button></p>
  </form>
</section>

<?php partial('partials/account-shell-end'); ?>
