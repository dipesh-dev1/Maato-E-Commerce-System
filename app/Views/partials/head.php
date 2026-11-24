<?php
/** Shared <head> contents. Expects $title, optional $metaDescription, $styles (list of css files). */
$storeName = setting('store_name', 'Maato');
$fullTitle = isset($title) && !str_contains((string) $title, $storeName) ? $title . ' · ' . $storeName : ($title ?? $storeName);
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($metaDescription ?? setting('store_tagline', '')) ?>">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<meta name="base-url" content="<?= e(base_path_url()) ?>">
<meta name="theme-color" content="#f4efe7">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:site_name" content="<?= e($storeName) ?>">
<link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..600,0..100,0..1;1,9..144,300..600,0..100,0..1&amp;family=Instrument+Sans:wght@400..700&amp;display=swap">
<link rel="stylesheet" href="<?= e(asset('css/tokens.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/base.css')) ?>">
<?php foreach ($styles ?? ['css/store.css'] as $sheet): ?>
<link rel="stylesheet" href="<?= e(asset($sheet)) ?>">
<?php endforeach; ?>
