-- --------------------------------------------------------
-- 004_cart.sql
-- カートテーブル（ログインユーザーごとのカート内商品）
-- 実行順: 4番目
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'カートID',
  `user_id` int(11) NOT NULL COMMENT 'ユーザーID（users.id）',
  `product_id` int(11) UNSIGNED NOT NULL COMMENT '商品ID（products.id）',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '数量',
  `add_date` date NOT NULL COMMENT '追加日（YYYY-MM-DD）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`, `product_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_product_id` (`product_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='カート';
