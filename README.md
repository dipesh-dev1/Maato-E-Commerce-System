# Maato: e-commerce store

A complete storefront and back office for **Maato**, a fictional homeware studio in Lalitpur selling stoneware, lighting, oak furniture, linen and apothecary from seven Nepali workshops. It is built with plain PHP (PDO, a small hand-written MVC), MySQL/MariaDB, vanilla ES-module JavaScript and hand-written CSS. Payment is cash on delivery or pay on pickup only.

- Storefront: home, category and search listings with live filters, product pages with variants, quick view, cart drawer, checkout, customer account.
- Moving to another computer: see [`INSTALL.md`](INSTALL.md).
- Back office (`/admin`): dashboard with a sales chart, products, categories, makers, coupons, orders (with invoice and CSV export), users, reviews, settings and an audit log.

## Requirements

- PHP **8.0 or newer** (developed on XAMPP's PHP 8.0.30 and written to run unchanged on 8.2+), with `pdo_mysql`, `mbstring`, `fileinfo`. `gd` is recommended (see below).
- MySQL 8 or MariaDB 10.4+.
- Apache with `mod_rewrite` and `AllowOverride All` (XAMPP and Laragon ship this way).

## Setup (XAMPP or Laragon)

1. Put the project at `C:\xampp\htdocs\suman-ecom` (Laragon: `C:\laragon\www\suman-ecom`).
2. Create `.env` from the example and adjust if needed:
   ```
   copy .env.example .env
   ```
   `APP_URL` must match the address you open in the browser, without a trailing slash, e.g. `http://localhost/suman-ecom` or `http://suman-ecom.test` on Laragon.
3. Create and fill the database. The name contains a hyphen, so the SQL files always quote it as `` `suman-ecom` ``:
   ```
   C:\xampp\mysql\bin\mysql -u root < database\schema.sql
   C:\xampp\mysql\bin\mysql -u root --default-character-set=utf8mb4 < database\seed.sql
   ```
   (or import both files in phpMyAdmin, schema first). Re-running both resets everything.
4. **Recommended:** enable GD so uploads are re-encoded and thumbnails are generated. In `C:\xampp\php\php.ini`, change `;extension=gd` to `extension=gd` and restart Apache. Without GD, uploads are still validated and stored safely but kept at their original size. The settings page warns while GD is off.
5. Open `http://localhost/suman-ecom`.

For anything other than local development, set `APP_DEBUG=false` and serve over HTTPS (the session cookie becomes `Secure` automatically).

## Seed logins

| Role | Email | Password |
|---|---|---|
| Admin | `admin@maato.test` | `Maato@Admin2026` |
| Staff | `staff@maato.test` | `Maato@Staff2026` |
| Customer (Lalitpur, 2 addresses, many orders) | `aarati@example.com` | `Customer@2026` |
| Customer (Pokhara, outside the valley) | `bikash@example.com` | `Customer@2026` |
| Customer (new this week) | `sneha@example.com` | `Customer@2026` |

Seed coupons: `NAMASTE10` (10% over Rs. 3,000), `TIHAR1500` (Rs. 1,500 off over Rs. 15,000), and `MONSOON15` (expired, to show the error). The seed also contains about 46 orders spread over the last 90 days, so the dashboard and chart have data. Order dates are relative to the moment you import.

**Password reset emails** are not sent. The link is appended to `storage/logs/mail.log`.

## Folder map

```
public/                 Web root (the only folder Apache serves)
  index.php             Front controller
  .htaccess             Rewrites everything that isn't a file to index.php
  assets/css            tokens.css, base.css, store.css, admin.css, print.css
  assets/js             store.js entry + modules; lib/ (http, dialog, toast, qty); admin/
  assets/icons.svg      The single SVG icon sprite
  assets/img            Brand photography, favicon, placeholder
app/
  bootstrap.php         Autoloader, .env, timezone
  routes.php            Every route, named, with middleware
  Core/                 App kernel, Router/Route, Request/Response, View, Session, Csrf, Auth,
                        Validator, Database, base Controller, helpers.php
  Middleware/           Authenticate, RedirectIfAuthenticated, BackOffice, Can, VerifyCsrf, SecurityHeaders
  Controllers/          Storefront controllers; Account/ (profile, orders, addresses); Admin/
  Models/               Repositories (one per aggregate), OrderStatus rules
  Services/             Cart, Checkout, Order, Auth, LoginThrottle, PasswordReset, Pricing, Settings,
                        ImageUploader, ActivityLogger, Mailer
  Views/                layouts/, partials/, store/, auth/, account/, admin/, errors/
config/                 app.php, database.php, permissions.php (role → abilities)
database/               schema.sql, seed.sql
storage/
  uploads/              Product, category, avatar and logo images (served by /media/…, outside the web root)
  logs/                 app.log (errors), mail.log (password-reset links)
docs/                   design-system.md, testing.md
.htaccess               Sends every request into public/ and denies dotfiles, .sql, .md, .log
```

## How it fits together

- `public/index.php` → `App::run()` captures the request, starts the session, matches a route, runs the middleware pipeline (CSRF on every write, then the route's own), calls the controller, adds security headers and sends the response.
- Controllers stay thin: they validate input with `Validator`, call a service or repository, and return a view or the standard JSON envelope `{ "ok": bool, "data": …, "error": string|null }` with a real HTTP status (422 validation, 401/403, 404, 419, 429).
- Every page works without JavaScript. Scripts add the cart drawer, quick view, live filters, instant search, slide-overs and the chart. Page-specific modules load on demand with dynamic `import()`.
- Prices are never stored in the cart; they are recalculated from the catalogue on each read and frozen into the order at checkout.

### Roles

Defined in `config/permissions.php`:

- **Admin:** everything.
- **Staff:** dashboard, products, categories and makers, orders, reviews. No users, coupons, settings or activity log.
- **Customer:** storefront and their own account.

Assumption: the brief lists categories/brands, coupons and reviews without assigning them to staff, so catalogue work and review moderation went to staff, and coupons (which change prices) stay admin-only.

## Security notes

- **SQL:** PDO with native prepared statements (`ATTR_EMULATE_PREPARES=false`) everywhere. Sort columns and directions come from whitelists; LIMIT/OFFSET are cast to int; LIKE wildcards in search terms are escaped; FULLTEXT boolean operators are stripped from user input.
- **Passwords:** Argon2id (bcrypt fallback), `password_verify`, transparent rehash on login. Seed hashes are bcrypt and upgrade on first login.
- **Sessions:** `HttpOnly`, `SameSite=Lax`, `Secure` on HTTPS, strict mode, 120-minute idle timeout, `session_regenerate_id` on login, password change and logout. A blocked account loses its session on its next request.
- **CSRF:** a per-session token is checked on every POST/PATCH/DELETE (form field or `X-CSRF-Token` header for fetch). Failure returns 419.
- **Login throttling:** 5 failures per email+IP or 20 per IP within 15 minutes lock that pair/IP until the window passes. Wrong-email and wrong-password return the same message, and an unknown email still runs a full password hash, so timing doesn't reveal it either. Password-reset requests are limited to 5 per IP per 15 minutes and always show the same confirmation.
- **Password reset:** 64-character random token; only its SHA-256 is stored; it expires after 30 minutes, works once, and a new request invalidates older links.
- **Output:** every dynamic value in views goes through `e()` (`htmlspecialchars`, `ENT_QUOTES`). JSON for scripts is placed in attributes through `e()`; JavaScript builds suggestion markup with `textContent`.
- **Headers:** a strict CSP (`script-src 'self'`, no inline script or style attributes anywhere), `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY` plus `frame-ancestors 'none'`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`, and `Cross-Origin-Opener-Policy`.
- **Uploads:** size limit (`UPLOAD_MAX_MB`), extension allow-list, real MIME type via `finfo` cross-checked with `getimagesize`, a pixel-count cap, random 128-bit filenames, and storage in `storage/uploads` outside the web root. Files are served by a route that only accepts image extensions inside that folder, with `nosniff` and `default-src 'none'`. With GD on, images are re-encoded, which strips metadata and anything appended to the pixels.
- **Authorisation and IDOR:** `/admin` requires admin or staff; each admin area requires its ability. Customer orders and addresses are always queried with `WHERE … AND user_id = ?`, so another customer's order number or address id returns 404. Admins can't block themselves or change their own role.
- **Redirects:** post-login "intended" URLs and `Back` redirects must be same-site paths.
- **Audit:** every back-office write (create, update, delete, status change, bulk action, export, invoice print, role change, block, settings) is written to `activity_logs` with user, IP, time and changed fields.
- **CSV export:** cells starting with `= + - @` are prefixed with `'` to stop formula injection in spreadsheets.
- **Secrets:** credentials live in `.env`, which is git-ignored and denied by `.htaccess`. `.env.example` documents every key.

## Testing and limitations

See [`docs/testing.md`](docs/testing.md) for what was tested and the known limitations.
