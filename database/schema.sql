-- Maato store schema. Creates (or recreates) every table in `suman-ecom`.
-- The database name contains a hyphen, so it is always written with backticks.

CREATE DATABASE IF NOT EXISTS `suman-ecom` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `suman-ecom`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS login_attempts, password_resets, activity_logs, settings, reviews,
    order_status_history, order_items, orders, coupons, wishlists, cart_items, carts,
    product_variants, product_images, products, brands, categories, addresses, users, roles;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
    id          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug        VARCHAR(20)  NOT NULL,
    name        VARCHAR(50)  NOT NULL,
    description VARCHAR(255) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    role_id       TINYINT UNSIGNED NOT NULL,
    name          VARCHAR(120)     NOT NULL,
    email         VARCHAR(190)     NOT NULL,
    phone         VARCHAR(20)      NULL,
    password_hash VARCHAR(255)     NOT NULL,
    avatar_path   VARCHAR(255)     NULL,
    is_blocked    TINYINT(1)       NOT NULL DEFAULT 0,
    last_login_at DATETIME         NULL,
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role_id),
    KEY idx_users_created (created_at),
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE addresses (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id        INT UNSIGNED NOT NULL,
    label          VARCHAR(40)  NOT NULL DEFAULT 'Home',
    recipient_name VARCHAR(120) NOT NULL,
    phone          VARCHAR(20)  NOT NULL,
    line1          VARCHAR(160) NOT NULL,
    line2          VARCHAR(160) NULL,
    city           VARCHAR(80)  NOT NULL,
    district       VARCHAR(80)  NOT NULL,
    province       VARCHAR(60)  NOT NULL,
    postal_code    VARCHAR(10)  NULL,
    is_default     TINYINT(1)   NOT NULL DEFAULT 0,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_addresses_user (user_id, is_default),
    CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id   INT UNSIGNED NULL,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL,
    tagline     VARCHAR(160) NULL,
    description TEXT         NULL,
    image_path  VARCHAR(255) NULL,
    sort_order  SMALLINT     NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug),
    KEY idx_categories_parent (parent_id, sort_order),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE brands (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL,
    origin      VARCHAR(100) NULL,
    description TEXT         NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_brands_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    category_id       INT UNSIGNED  NOT NULL,
    brand_id          INT UNSIGNED  NULL,
    name              VARCHAR(160)  NOT NULL,
    slug              VARCHAR(180)  NOT NULL,
    short_description VARCHAR(255)  NOT NULL DEFAULT '',
    description       TEXT          NOT NULL,
    details           TEXT          NULL COMMENT 'Materials, dimensions and care, one per line',
    price             DECIMAL(12,2) NOT NULL,
    sale_price        DECIMAL(12,2) NULL,
    sale_starts_at    DATETIME      NULL,
    sale_ends_at      DATETIME      NULL,
    status            ENUM('draft','published') NOT NULL DEFAULT 'draft',
    is_featured       TINYINT(1)    NOT NULL DEFAULT 0,
    size_guide        ENUM('bedding','rug','furniture') NULL,
    meta_title        VARCHAR(160)  NULL,
    meta_description  VARCHAR(255)  NULL,
    created_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at        DATETIME      NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_slug (slug),
    KEY idx_products_listing (status, deleted_at, created_at),
    KEY idx_products_category (category_id),
    KEY idx_products_brand (brand_id),
    FULLTEXT KEY ft_products_search (name, short_description, description),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT,
    CONSTRAINT fk_products_brand FOREIGN KEY (brand_id) REFERENCES brands (id) ON DELETE SET NULL,
    CONSTRAINT chk_products_price CHECK (price >= 0 AND (sale_price IS NULL OR sale_price >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED NOT NULL,
    path       VARCHAR(255) NOT NULL,
    thumb_path VARCHAR(255) NOT NULL,
    alt        VARCHAR(160) NOT NULL DEFAULT '',
    sort_order SMALLINT     NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_product_images_order (product_id, sort_order),
    CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_variants (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED  NOT NULL,
    sku        VARCHAR(64)   NOT NULL,
    size       VARCHAR(40)   NULL,
    color      VARCHAR(40)   NULL,
    color_hex  CHAR(7)       NULL,
    price      DECIMAL(12,2) NULL COMMENT 'NULL = use product price',
    sale_price DECIMAL(12,2) NULL COMMENT 'NULL = use product sale price',
    stock      INT UNSIGNED  NOT NULL DEFAULT 0,
    sort_order SMALLINT      NOT NULL DEFAULT 0,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_variants_sku (sku),
    KEY idx_variants_product (product_id, sort_order),
    KEY idx_variants_stock (stock),
    CONSTRAINT fk_variants_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE carts (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carts_user (user_id),
    CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
    id         INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    cart_id    INT UNSIGNED      NOT NULL,
    variant_id INT UNSIGNED      NOT NULL,
    quantity   SMALLINT UNSIGNED NOT NULL,
    created_at DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cart_items (cart_id, variant_id),
    KEY idx_cart_items_variant (variant_id),
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants (id) ON DELETE CASCADE,
    CONSTRAINT chk_cart_items_qty CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wishlists (
    user_id    INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, product_id),
    KEY idx_wishlists_product (product_id),
    CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE coupons (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    code           VARCHAR(30)   NOT NULL,
    description    VARCHAR(160)  NULL,
    type           ENUM('percent','fixed') NOT NULL,
    value          DECIMAL(10,2) NOT NULL,
    min_cart_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    usage_limit    INT UNSIGNED  NULL,
    used_count     INT UNSIGNED  NOT NULL DEFAULT 0,
    starts_at      DATETIME      NULL,
    expires_at     DATETIME      NULL,
    is_active      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_coupons_code (code),
    CONSTRAINT chk_coupons_value CHECK (value > 0 AND (type <> 'percent' OR value <= 100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    order_number     VARCHAR(20)   NOT NULL,
    user_id          INT UNSIGNED  NOT NULL,
    status           ENUM('pending','confirmed','packed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    payment_method   ENUM('cash_on_delivery','pay_on_pickup') NOT NULL,
    subtotal         DECIMAL(12,2) NOT NULL,
    discount_total   DECIMAL(12,2) NOT NULL DEFAULT 0,
    shipping_total   DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax_rate         DECIMAL(5,2)  NOT NULL DEFAULT 0,
    tax_total        DECIMAL(12,2) NOT NULL DEFAULT 0,
    grand_total      DECIMAL(12,2) NOT NULL,
    currency         CHAR(3)       NOT NULL DEFAULT 'NPR',
    coupon_id        INT UNSIGNED  NULL,
    coupon_code      VARCHAR(30)   NULL,
    -- Snapshots: what the customer saw and where it went, frozen at checkout.
    customer_name    VARCHAR(120)  NOT NULL,
    customer_email   VARCHAR(190)  NOT NULL,
    customer_phone   VARCHAR(20)   NULL,
    ship_recipient   VARCHAR(120)  NOT NULL,
    ship_phone       VARCHAR(20)   NOT NULL,
    ship_line1       VARCHAR(160)  NOT NULL,
    ship_line2       VARCHAR(160)  NULL,
    ship_city        VARCHAR(80)   NOT NULL,
    ship_district    VARCHAR(80)   NOT NULL,
    ship_province    VARCHAR(60)   NOT NULL,
    ship_postal_code VARCHAR(10)   NULL,
    note             VARCHAR(500)  NULL,
    cancelled_at     DATETIME      NULL,
    delivered_at     DATETIME      NULL,
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_orders_number (order_number),
    KEY idx_orders_user (user_id, created_at),
    KEY idx_orders_status (status, created_at),
    KEY idx_orders_created (created_at),
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id            INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    order_id      INT UNSIGNED      NOT NULL,
    product_id    INT UNSIGNED      NULL,
    variant_id    INT UNSIGNED      NULL,
    product_name  VARCHAR(160)      NOT NULL,
    variant_label VARCHAR(90)       NULL,
    sku           VARCHAR(64)       NOT NULL,
    image_path    VARCHAR(255)      NULL,
    unit_price    DECIMAL(12,2)     NOT NULL,
    quantity      SMALLINT UNSIGNED NOT NULL,
    line_total    DECIMAL(12,2)     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_order_items_order (order_id),
    KEY idx_order_items_product (product_id),
    KEY idx_order_items_variant (variant_id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL,
    CONSTRAINT fk_order_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_status_history (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id   INT UNSIGNED NOT NULL,
    status     ENUM('pending','confirmed','packed','shipped','delivered','cancelled') NOT NULL,
    note       VARCHAR(500) NULL,
    changed_by INT UNSIGNED NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_status_history_order (order_id, created_at),
    CONSTRAINT fk_status_history_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_status_history_user FOREIGN KEY (changed_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED     NOT NULL,
    user_id    INT UNSIGNED     NOT NULL,
    rating     TINYINT UNSIGNED NOT NULL,
    title      VARCHAR(120)     NOT NULL,
    body       TEXT             NOT NULL,
    status     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reviews_user_product (product_id, user_id),
    KEY idx_reviews_status (status, created_at),
    KEY idx_reviews_user (user_id),
    CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    `key`      VARCHAR(60) NOT NULL,
    value      TEXT        NULL,
    updated_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED    NULL,
    action      VARCHAR(60)     NOT NULL,
    entity_type VARCHAR(40)     NOT NULL,
    entity_id   INT UNSIGNED    NULL,
    description VARCHAR(255)    NOT NULL,
    meta        TEXT            NULL COMMENT 'JSON',
    ip_address  VARCHAR(45)     NOT NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_activity_created (created_at),
    KEY idx_activity_user (user_id, created_at),
    KEY idx_activity_entity (entity_type, entity_id),
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    token_hash CHAR(64)     NOT NULL COMMENT 'sha256 of the emailed token; the raw token is never stored',
    expires_at DATETIME     NOT NULL,
    used_at    DATETIME     NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_password_resets_token (token_hash),
    KEY idx_password_resets_user (user_id),
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email        VARCHAR(190)    NOT NULL,
    ip_address   VARCHAR(45)     NOT NULL,
    successful   TINYINT(1)      NOT NULL DEFAULT 0,
    attempted_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_login_attempts_email_ip (email, ip_address, attempted_at),
    KEY idx_login_attempts_ip (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
