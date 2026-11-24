# Maato-E-Commerce-System
Maato, a full-stack e-commerce store for a homeware business. Built with plain PHP 8, PDO and MySQL/MariaDB using a hand-written MVC structure, with no framework. Includes a storefront, cart, checkout, customer accounts, reviews and an admin back office.

## Features
- Storefront: shop and category listings, search with instant suggestions,
  filters and sorting, product pages with size and colour variants, quick view
- Shopping bag and cart drawer for guests and customers, coupon codes
- Checkout with cash on delivery or pay on pickup. Delivery fee, discount and
  VAT are calculated automatically, and stock is checked inside a transaction
- Customer accounts: registration, login, password reset, addresses, order
  history, order cancellation, wishlist and reviews
- Back office (/admin): sales dashboard, products with variants and images,
  categories, makers, orders (status changes, CSV export, printable invoice),
  coupons, users, review moderation, settings and an activity log
- Role-based access (admin, staff, customer)
- Security: prepared statements, Argon2id passwords, CSRF protection, login
  throttling, a strict Content Security Policy and checked image uploads

## Tech stack
PHP 8, MySQL/MariaDB, Apache (XAMPP), HTML, CSS, JavaScript (ES modules)

## Setup
See INSTALL.md for step-by-step instructions.
