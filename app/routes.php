<?php
declare(strict_types=1);

use App\Controllers\Account;
use App\Controllers\Admin;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CatalogController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Controllers\ReviewController;
use App\Controllers\WishlistController;

/** @var App\Core\Router $router provided by App::router() */

// ---- Storefront ----
$router->get('/', [HomeController::class, 'index'])->name('home');
$router->get('/shop', [CatalogController::class, 'index'])->name('shop');
$router->get('/category/{slug}', [CatalogController::class, 'category'])->name('category');
$router->get('/search', [CatalogController::class, 'search'])->name('search');
$router->get('/search/suggest', [CatalogController::class, 'suggest'])->name('search.suggest');
$router->get('/product/{slug}', [ProductController::class, 'show'])->name('product');
$router->get('/product/{id:\d+}/quick-view', [ProductController::class, 'quickView'])->name('product.quick');
$router->post('/product/{slug}/reviews', [ReviewController::class, 'store'])->name('review.store')->middleware('auth');
$router->get('/pages/{page}', [PageController::class, 'show'])->name('page');

$router->get('/cart', [CartController::class, 'show'])->name('cart');
$router->get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');
$router->post('/cart/items', [CartController::class, 'add'])->name('cart.add');
$router->patch('/cart/items/{variant:\d+}', [CartController::class, 'update'])->name('cart.update');
$router->delete('/cart/items/{variant:\d+}', [CartController::class, 'remove'])->name('cart.remove');
$router->post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
$router->delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

$router->post('/wishlist/{product:\d+}', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');

$router->get('/checkout', [CheckoutController::class, 'show'])->name('checkout')->middleware('auth');
$router->post('/checkout', [CheckoutController::class, 'place'])->name('checkout.place')->middleware('auth');

// ---- Auth ----
$router->group('', ['guest'], static function ($r): void {
    $r->get('/login', [AuthController::class, 'showLogin'])->name('login');
    $r->post('/login', [AuthController::class, 'login']);
    $r->get('/register', [AuthController::class, 'showRegister'])->name('register');
    $r->post('/register', [AuthController::class, 'register']);
    $r->get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.forgot');
    $r->post('/forgot-password', [AuthController::class, 'sendReset']);
    $r->get('/reset-password/{token:[a-f0-9]+}', [AuthController::class, 'showReset'])->name('password.reset');
    $r->post('/reset-password/{token:[a-f0-9]+}', [AuthController::class, 'reset']);
});
$router->post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---- Customer account ----
$router->group('/account', ['auth'], static function ($r): void {
    $r->get('', [Account\ProfileController::class, 'show'])->name('account');
    $r->post('/profile', [Account\ProfileController::class, 'update'])->name('account.profile');
    $r->post('/password', [Account\ProfileController::class, 'password'])->name('account.password');
    $r->post('/avatar', [Account\ProfileController::class, 'avatar'])->name('account.avatar');
    $r->delete('/avatar', [Account\ProfileController::class, 'removeAvatar'])->name('account.avatar.remove');

    $r->get('/orders', [Account\OrderController::class, 'index'])->name('account.orders');
    $r->get('/orders/{number:MT\d+}', [Account\OrderController::class, 'show'])->name('account.order');
    $r->post('/orders/{number:MT\d+}/cancel', [Account\OrderController::class, 'cancel'])->name('account.order.cancel');

    $r->get('/addresses', [Account\AddressController::class, 'index'])->name('account.addresses');
    $r->post('/addresses', [Account\AddressController::class, 'store'])->name('account.addresses.store');
    $r->patch('/addresses/{id:\d+}', [Account\AddressController::class, 'update'])->name('account.addresses.update');
    $r->delete('/addresses/{id:\d+}', [Account\AddressController::class, 'destroy'])->name('account.addresses.destroy');
    $r->post('/addresses/{id:\d+}/default', [Account\AddressController::class, 'makeDefault'])->name('account.addresses.default');

    $r->get('/wishlist', [WishlistController::class, 'index'])->name('account.wishlist');
});

// ---- Back office ----
$router->group('/admin', ['backoffice'], static function ($r): void {
    $r->get('', [Admin\DashboardController::class, 'index'])->name('admin.dashboard')->middleware('can:dashboard.view');
    $r->get('/sales', [Admin\DashboardController::class, 'sales'])->name('admin.sales')->middleware('can:dashboard.view');

    $r->group('/products', ['can:products.manage'], static function ($r): void {
        $r->get('', [Admin\ProductController::class, 'index'])->name('admin.products');
        $r->get('/create', [Admin\ProductController::class, 'create'])->name('admin.products.create');
        $r->post('', [Admin\ProductController::class, 'store'])->name('admin.products.store');
        $r->post('/bulk', [Admin\ProductController::class, 'bulk'])->name('admin.products.bulk');
        $r->get('/{id:\d+}/edit', [Admin\ProductController::class, 'edit'])->name('admin.products.edit');
        $r->patch('/{id:\d+}', [Admin\ProductController::class, 'update'])->name('admin.products.update');
        $r->delete('/{id:\d+}', [Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');
    });

    $r->group('', ['can:catalog.manage'], static function ($r): void {
        $r->get('/categories', [Admin\CategoryController::class, 'index'])->name('admin.categories');
        $r->post('/categories', [Admin\CategoryController::class, 'store'])->name('admin.categories.store');
        $r->patch('/categories/{id:\d+}', [Admin\CategoryController::class, 'update'])->name('admin.categories.update');
        $r->delete('/categories/{id:\d+}', [Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
        $r->get('/brands', [Admin\BrandController::class, 'index'])->name('admin.brands');
        $r->post('/brands', [Admin\BrandController::class, 'store'])->name('admin.brands.store');
        $r->patch('/brands/{id:\d+}', [Admin\BrandController::class, 'update'])->name('admin.brands.update');
        $r->delete('/brands/{id:\d+}', [Admin\BrandController::class, 'destroy'])->name('admin.brands.destroy');
    });

    $r->group('/coupons', ['can:coupons.manage'], static function ($r): void {
        $r->get('', [Admin\CouponController::class, 'index'])->name('admin.coupons');
        $r->post('', [Admin\CouponController::class, 'store'])->name('admin.coupons.store');
        $r->patch('/{id:\d+}', [Admin\CouponController::class, 'update'])->name('admin.coupons.update');
        $r->delete('/{id:\d+}', [Admin\CouponController::class, 'destroy'])->name('admin.coupons.destroy');
    });

    $r->group('/orders', ['can:orders.manage'], static function ($r): void {
        $r->get('', [Admin\OrderController::class, 'index'])->name('admin.orders');
        $r->get('/export', [Admin\OrderController::class, 'export'])->name('admin.orders.export');
        $r->post('/bulk', [Admin\OrderController::class, 'bulk'])->name('admin.orders.bulk');
        $r->get('/{id:\d+}', [Admin\OrderController::class, 'show'])->name('admin.orders.show');
        $r->post('/{id:\d+}/status', [Admin\OrderController::class, 'status'])->name('admin.orders.status');
        $r->get('/{id:\d+}/invoice', [Admin\OrderController::class, 'invoice'])->name('admin.orders.invoice');
    });

    $r->group('/users', ['can:users.manage'], static function ($r): void {
        $r->get('', [Admin\UserController::class, 'index'])->name('admin.users');
        $r->get('/{id:\d+}', [Admin\UserController::class, 'show'])->name('admin.users.show');
        $r->post('/{id:\d+}/block', [Admin\UserController::class, 'toggleBlock'])->name('admin.users.block');
        $r->post('/{id:\d+}/role', [Admin\UserController::class, 'role'])->name('admin.users.role');
    });

    $r->group('/reviews', ['can:reviews.manage'], static function ($r): void {
        $r->get('', [Admin\ReviewController::class, 'index'])->name('admin.reviews');
        $r->post('/bulk', [Admin\ReviewController::class, 'bulk'])->name('admin.reviews.bulk');
    });

    $r->get('/settings', [Admin\SettingController::class, 'edit'])->name('admin.settings')->middleware('can:settings.manage');
    $r->post('/settings', [Admin\SettingController::class, 'update'])->name('admin.settings.update')->middleware('can:settings.manage');
    $r->get('/activity', [Admin\ActivityController::class, 'index'])->name('admin.activity')->middleware('can:activity.view');
});
