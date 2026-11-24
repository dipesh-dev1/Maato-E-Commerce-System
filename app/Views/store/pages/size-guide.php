<?php partial('partials/breadcrumb', ['crumbs' => [['Size guide', null]]]); ?>
<div class="container">
  <header class="page-hero">
    <h1 class="h1">Size guide</h1>
    <p>Bedding, rugs and furniture, measured the way we'd want to be told. When in doubt, measure twice and message us a photo.</p>
  </header>
  <div class="page-body">
    <aside class="panel stack" aria-label="Need help measuring?">
      <p class="eyebrow">Not sure?</p>
      <p>Send a photo of the space and a rough measurement to <a href="mailto:<?= e(setting('support_email')) ?>"><?= e(setting('support_email')) ?></a>. We'll tell you honestly what fits.</p>
    </aside>
    <div class="stack stack--loose"><?php partial('store/partials/size-tables', ['type' => 'all']); ?></div>
  </div>
</div>
