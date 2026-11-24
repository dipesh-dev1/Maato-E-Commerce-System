<?php /** Expects $navTree. */ $user = auth_user(); ?>
<footer class="site-footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <?php partial('partials/logo'); ?>
        <p>Homeware made slowly, by seven small workshops across Nepal. Designed in Jhamsikhel, made in Thimi, Patan, Chitwan, Pokhara, Gorkha and Jumla.</p>
        <div class="footer__contact">
          <a href="mailto:<?= e(setting('support_email')) ?>"><?= icon('mail', 'icon--sm') ?> <?= e(setting('support_email')) ?></a>
          <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) setting('support_phone'))) ?>"><?= icon('phone', 'icon--sm') ?> <?= e(setting('support_phone')) ?></a>
        </div>
      </div>
      <div class="footer__col">
        <h2>Shop</h2>
        <ul>
          <?php foreach ($navTree as $cat): ?>
            <li><a href="<?= e(route('category', ['slug' => $cat['slug']])) ?>"><?= e($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="footer__col">
        <h2>Help</h2>
        <ul>
          <li><a href="<?= e(route('page', ['page' => 'shipping-returns'])) ?>">Delivery &amp; returns</a></li>
          <li><a href="<?= e(route('page', ['page' => 'size-guide'])) ?>">Size guide</a></li>
          <li><a href="<?= e(route('page', ['page' => 'contact'])) ?>">Contact us</a></li>
          <li><a href="<?= e(route('page', ['page' => 'our-story'])) ?>">Our story</a></li>
        </ul>
      </div>
      <div class="footer__col">
        <h2>Account</h2>
        <ul>
          <?php if ($user): ?>
            <li><a href="<?= e(route('account')) ?>">Your account</a></li>
            <li><a href="<?= e(route('account.orders')) ?>">Orders</a></li>
            <li><a href="<?= e(route('account.wishlist')) ?>">Saved for later</a></li>
          <?php else: ?>
            <li><a href="<?= e(route('login')) ?>">Sign in</a></li>
            <li><a href="<?= e(route('register')) ?>">Create an account</a></li>
          <?php endif; ?>
          <li><a href="<?= e(route('cart')) ?>">Your bag</a></li>
        </ul>
      </div>
    </div>
    <div class="footer__wordmark" aria-hidden="true">maato</div>
    <div class="footer__base">
      <span>© <?= date('Y') ?> <?= e(setting('store_name', 'Maato')) ?>. Prices in <?= e(setting('currency_code', 'NPR')) ?>; <?= e(setting('tax_label', 'VAT')) ?> is added at checkout.</span>
      <span>We take cash on delivery and payment on pickup. No cards needed.</span>
    </div>
  </div>
</footer>
