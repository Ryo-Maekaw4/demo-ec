-- --------------------------------------------------------
-- 002_categories.sql
-- カテゴリマスタテーブル（商品の category_id が参照する）
-- 実行順: 2番目（products より先に実行すること）
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'カテゴリID',
  `name` varchar(100) NOT NULL COMMENT 'カテゴリ名',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT '表示順',
  `created_at` int(11) NOT NULL DEFAULT 0 COMMENT '作成日時',
  `updated_at` int(11) NOT NULL DEFAULT 0 COMMENT '更新日時',
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='カテゴリマスタ';

-- マスタデータ: 1:家具、2:家電、3:食品
INSERT INTO `categories` (`id`, `name`, `display_order`, `created_at`, `updated_at`) VALUES
(1, '家具', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, '家電', 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, '食品', 3, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `display_order` = VALUES(`display_order`), `updated_at` = UNIX_TIMESTAMP();
