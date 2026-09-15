-- ==========================================================
-- PHASE 2 MIGRATION SCRIPT
-- Maps legacy tables (coustmrs, prod) to API tables (users, products)
-- Adds necessary columns and creates new E-commerce tables
-- ==========================================================

-- 1. Migrate Users Table
RENAME TABLE `coustmrs` TO `users`;

ALTER TABLE `users`
  CHANGE `user_gmail` `user_email` varchar(100) NOT NULL,
  CHANGE `user_per` `user_role` int(11) NOT NULL DEFAULT 0,
  CHANGE `creat_acoount` `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  CHANGE `update_acount` `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  ADD COLUMN `api_token` varchar(255) DEFAULT NULL AFTER `user_role`;


-- 2. Migrate Products Table
RENAME TABLE `prod` TO `products`;

-- Clean price string (e.g. remove $ signs) before converting to decimal
UPDATE `products` SET `prod_price` = REPLACE(`prod_price`, '$', '');

ALTER TABLE `products`
  CHANGE `prod_price` `prod_price` decimal(10,2) NOT NULL,
  ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp();


-- 3. Create Cart & Order Tables
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_product_unique` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
