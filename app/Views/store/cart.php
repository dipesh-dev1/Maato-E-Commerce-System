<?php partial('partials/breadcrumb', ['crumbs' => [['Your bag', null]]]); ?>
<div class="container">
  <header class="listing-head"><h1 class="h1">Your bag</h1></header>
  <div data-cart-page>
    <?php partial('store/partials/cart-page-body', ['lines' => $lines, 'summary' => $summary]); ?>
  </div>
</div>
