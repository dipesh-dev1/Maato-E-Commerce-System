<?php
// Role slug => granted abilities. '*' grants everything.
// Staff run the shop floor (catalogue, orders, reviews); people, money rules and settings stay with admins.
return [
    'admin'    => ['*'],
    'staff'    => ['dashboard.view', 'products.manage', 'catalog.manage', 'orders.manage', 'reviews.manage'],
    'customer' => [],
];
