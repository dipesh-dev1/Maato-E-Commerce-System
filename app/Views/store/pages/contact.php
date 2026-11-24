<?php partial('partials/breadcrumb', ['crumbs' => [['Contact us', null]]]); ?>
<div class="container page-end">
  <header class="page-hero">
    <h1 class="h1">Talk to a person</h1>
    <p>Prakriti and Nabin answer every message themselves, usually within a working day. The studio is open Sunday to Friday, 10:00 to 18:00.</p>
  </header>
  <div class="contact-cards">
    <div class="panel"><?= icon('phone') ?><h2 class="h4">Call</h2><p><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) setting('support_phone'))) ?>"><?= e(setting('support_phone')) ?></a></p><p class="small muted">Best for changing an order that's already confirmed.</p></div>
    <div class="panel"><?= icon('mail') ?><h2 class="h4">Email</h2><p><a href="mailto:<?= e(setting('support_email')) ?>"><?= e(setting('support_email')) ?></a></p><p class="small muted">Best for photos of a damaged delivery or trade enquiries.</p></div>
    <div class="panel"><?= icon('pin') ?><h2 class="h4">Visit</h2><p><?= e(setting('pickup_address')) ?></p><p class="small muted">Come and sit on the sofas before you buy one.</p></div>
  </div>
</div>
