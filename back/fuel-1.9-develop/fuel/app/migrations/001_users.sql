-- --------------------------------------------------------
-- 001_users.sql
-- ユーザーテーブル（fuel_dev の users 構造に合わせる）
-- 実行順: 1番目
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ユーザーID',
  `email` varchar(255) NOT NULL COMMENT 'メールアドレス（ユニーク）',
  `password` varchar(255) NOT NULL COMMENT 'パスワード（ハッシュ化）',
  `salt` varchar(255) NOT NULL DEFAULT '' COMMENT 'ソルト',
  `name` varchar(255) NOT NULL COMMENT 'ユーザー名',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '作成日時',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新日時',
  `username` varchar(255) NOT NULL COMMENT 'ログインID',
  `last_login` int(11) DEFAULT NULL COMMENT '最終ログイン（Unix時刻）',
  `login_hash` varchar(255) DEFAULT NULL COMMENT 'ログインハッシュ',
  `group` int(11) NOT NULL DEFAULT 1 COMMENT 'グループID',
  `profile_fields` text DEFAULT NULL COMMENT '追加プロフィール（JSON等）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_group` (`group`),
  KEY `idx_login_hash` (`login_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='ユーザーテーブル';

-- サンプルユーザー
-- パスワード規則: メールの@より前の文字列 + "123"
-- 暗号化していないパスワード（参考）:
--   id=1 demo@example.com  → demo123
--   id=2 admin@example.com → admin123
--   id=3 test@example.com → test123
INSERT INTO `users` (`id`, `email`, `password`, `salt`, `name`, `created_at`, `updated_at`, `username`, `last_login`, `login_hash`, `group`, `profile_fields`) VALUES
(1, 'demo@example.com', '$2y$10$pZeArB/2PpgajoymNF3uC.6lTvXt5q57BwvnDX8vV//vH9cdYlWfq', '', 'デモユーザー', '2026-01-09 16:59:34', '2026-01-09 17:23:48', '', NULL, NULL, 1, NULL),
(2, 'admin@example.com', '$2y$10$oUAXaauKGx2w6SLGbaTKB.T1RhXf/fRqZtw0jqQRqzHgvJW1qUTa.', '', '管理者', '2026-01-09 16:59:34', '2026-01-09 17:23:48', '', NULL, NULL, 1, NULL),
(3, 'test@example.com', '$2y$10$SKDgHCU88f9D2o8aUjChWeLU/Cm0rJL6hd4U105xb5OktYG1zkZRS', '', '', '0000-00-00 00:00:00', '2026-01-23 11:15:10', 'test_user', 1769134510, 'bf9fdf584f4a26eb3a573279a1a2fc24f9ec7c55', 1, 'a:0:{}')
ON DUPLICATE KEY UPDATE `updated_at` = VALUES(`updated_at`);
