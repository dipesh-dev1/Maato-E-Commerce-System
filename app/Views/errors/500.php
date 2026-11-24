<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Something went wrong · Maato</title>
<link rel="stylesheet" href="<?= e(url('/assets/css/tokens.css')) ?>">
<link rel="stylesheet" href="<?= e(url('/assets/css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(url('/assets/css/store.css')) ?>">
</head>
<body>
<main class="container error-page">
  <div>
    <p class="error-page__code" aria-hidden="true">500</p>
    <h1 class="h2">Something went wrong on our side.</h1>
    <p class="lede">It isn't you. We've logged the problem and will look at it. Please try again in a minute; your bag is safe.</p>
    <?php if (!empty($detail)): ?><p class="notice notice--danger"><?= e($detail) ?></p><?php endif; ?>
    <p><a class="btn" href="<?= e(url('/')) ?>">Back to the homepage</a></p>
  </div>
</main>
</body>
</html>
