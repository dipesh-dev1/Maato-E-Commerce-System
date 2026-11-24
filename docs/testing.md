# What was tested

All checks were run against the local XAMPP stack (Apache, PHP 8.0.30, MariaDB 10.4.32) with the seed data. There is no automated test suite. These were scripted HTTP checks with curl, database assertions, and in-browser JavaScript checks in Chrome.

## Storefront and checkout
- All public routes return 200; unknown URLs show the 404 page; `/checkout`, `/account` and `/admin` redirect guests to sign-in.
- `.env`, `/storage/logs/*`, `/app/*` and `.sql` files are not reachable over HTTP.
- Product variant picker: switching size and colour updates price, stock note, SKU and the add button; unavailable combinations are marked; a sold-out variant disables the button.
- Add to bag from the product page and from quick view; the drawer opens with the line; the count bubble updates.
- Cart: the quantity stepper (debounced), remove, and coupon apply (`NAMASTE10`). Totals were checked by hand: 2 × Rs. 19,500, less 10%, plus 13% VAT on Rs. 35,100, gives Rs. 39,663.
- Guest bag merges into the account on login.
- Checkout as an outside-valley customer: the delivery fee follows the address; pickup hides the address step and sets delivery to free; the order is created with item, price and address snapshots; stock decremented (3 → 1); coupon usage incremented; status history written; bag cleared.
- Stock drops to 0 between adding and checking out: the order is refused with a clear message and nothing is written.
- Customer cancel while pending restocks exactly once; a second cancel is refused.
- Listing filters (in stock, price, sort) and pagination update the results and the URL; instant search suggestions highlight the match and respond to arrow keys.
- No horizontal overflow on home, shop, category, product, cart or the admin product editor at a 307px-wide viewport (narrower than the 360px target).
- No browser console errors on the pages exercised.

## Security
- POST without a CSRF token returns 419.
- IDOR: opening another customer's order number and updating another customer's address both return 404.
- A customer opening `/admin` gets 403; staff opening users, settings, coupons or the activity log get 403.
- Login lockout after repeated failures, even with the correct password afterwards; an unknown email gets the same message as a wrong password.
- Password-reset link is written to `storage/logs/mail.log`, opens the reset form, and only the hash is stored.
- An upload named `evil.php.jpg` that contains PHP is rejected with a clear warning, while the valid image in the same request is saved.

## Back office
- Every admin page loads for admin; staff see only their areas.
- Product create with two variants and an image; update keeps the existing variant id (so carts and history stay linked) and removes the dropped variant; a sale price shows in the shop.
- Validation: duplicate SKU, sale price ≥ price, duplicate slug, a percentage coupon over 100, and pending → delivered are all refused with specific messages; slide-over forms show errors inline.
- Order status change with a note, bulk "mark packed", review bulk approve, block and unblock, role change, and refusal to self-block.
- CSV export (UTF-8 with BOM) and the printable invoice.
- Data table search, sort and select-all; slide-over edit fills the form; product editor variant rows and image reordering; dashboard chart range switch, tooltip and table view.
- The activity log recorded every one of the actions above.

# Known limitations
- **Visual review:** the browser tool's screenshot capture failed in this environment, so layout was verified by measuring the DOM (overflow, sizes, state) rather than by eye. Give the pages a visual once-over at a few widths.
- **PHP version:** XAMPP here ships PHP 8.0, so the code avoids 8.1+ syntax (enums, `readonly`) to run on 8.0 through 8.3.
- **GD is disabled** in the bundled `php.ini`. Without it, uploads are stored at original size with no re-encode, and the "thumbnail" is a copy. Enable `extension=gd` for proper thumbnails.
- **Email** is logged to `storage/logs/mail.log`; there is no SMTP transport.
- **Sessions are file-based**, so resetting a password cannot sign out the user's other existing sessions; they expire after the idle timeout.
- **Login throttling** keys on `REMOTE_ADDR`; behind a reverse proxy, configure Apache to pass the real client IP.
- **Order numbers** are random six-digit numbers with a uniqueness check, not sequential invoice numbers.
- **Search** uses MariaDB FULLTEXT in boolean mode; words under 3 characters fall back to a LIKE match on name and summary.
- **Route patterns** don't support regex quantifiers with braces (e.g. `{64}`) inside `{param:…}`.
- **Category cover images** are validated after the category row is saved, so a rejected image still leaves the category created, with an error shown.
- **Prices exclude VAT**, which is added at checkout; there is no tax-inclusive mode.
- **Seed photography** comes from Unsplash (free licence). The brand, makers and reviews are fictional.
