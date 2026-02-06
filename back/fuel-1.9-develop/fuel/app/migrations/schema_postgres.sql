-- --------------------------------------------------------
-- schema_postgres.sql (PostgreSQL 用 全テーブル一括)
-- 001_users, 002_categories, 003_products, 004_cart を順に実行
-- 実行: psql -U user -d dbname -f schema_postgres.sql
-- --------------------------------------------------------

-- ============================================================
-- 1. users（ユーザーテーブル）
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
  id serial PRIMARY KEY,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  salt varchar(255) NOT NULL DEFAULT '',
  name varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  username varchar(255) NOT NULL,
  last_login integer DEFAULT NULL,
  login_hash varchar(255) DEFAULT NULL,
  "group" integer NOT NULL DEFAULT 1,
  profile_fields text DEFAULT NULL,
  UNIQUE (email)
);

COMMENT ON TABLE users IS 'ユーザーテーブル';
COMMENT ON COLUMN users.id IS 'ユーザーID';
COMMENT ON COLUMN users.email IS 'メールアドレス（ユニーク）';
COMMENT ON COLUMN users.password IS 'パスワード（ハッシュ化）';
COMMENT ON COLUMN users.salt IS 'ソルト';
COMMENT ON COLUMN users.name IS 'ユーザー名';
COMMENT ON COLUMN users.created_at IS '作成日時';
COMMENT ON COLUMN users.updated_at IS '更新日時';
COMMENT ON COLUMN users.username IS 'ログインID';
COMMENT ON COLUMN users.last_login IS '最終ログイン（Unix時刻）';
COMMENT ON COLUMN users.login_hash IS 'ログインハッシュ';
COMMENT ON COLUMN users."group" IS 'グループID';
COMMENT ON COLUMN users.profile_fields IS '追加プロフィール（JSON等）';

CREATE INDEX IF NOT EXISTS idx_users_group ON users ("group");
CREATE INDEX IF NOT EXISTS idx_users_login_hash ON users (login_hash);

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = CURRENT_TIMESTAMP;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trigger_users_updated_at ON users;
CREATE TRIGGER trigger_users_updated_at
  BEFORE UPDATE ON users
  FOR EACH ROW EXECUTE PROCEDURE update_updated_at_column();

-- サンプルユーザー（パスワード: メール@より前 + "123"）
INSERT INTO users (id, email, password, salt, name, created_at, updated_at, username, last_login, login_hash, "group", profile_fields) VALUES
(1, 'demo@example.com', '$2y$10$pZeArB/2PpgajoymNF3uC.6lTvXt5q57BwvnDX8vV//vH9cdYlWfq', '', 'デモユーザー', '2026-01-09 16:59:34', '2026-01-09 17:23:48', '', NULL, NULL, 1, NULL),
(2, 'admin@example.com', '$2y$10$oUAXaauKGx2w6SLGbaTKB.T1RhXf/fRqZtw0jqQRqzHgvJW1qUTa.', '', '管理者', '2026-01-09 16:59:34', '2026-01-09 17:23:48', '', NULL, NULL, 1, NULL),
(3, 'test@example.com', '$2y$10$SKDgHCU88f9D2o8aUjChWeLU/Cm0rJL6hd4U105xb5OktYG1zkZRS', '', '', '1970-01-01 00:00:00', '2026-01-23 11:15:10', 'test_user', 1769134510, 'bf9fdf584f4a26eb3a573279a1a2fc24f9ec7c55', 1, 'a:0:{}')
ON CONFLICT (id) DO UPDATE SET updated_at = EXCLUDED.updated_at;

SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE((SELECT MAX(id) FROM users), 1));

-- ============================================================
-- 2. categories（カテゴリマスタ）
-- ============================================================

CREATE TABLE IF NOT EXISTS categories (
  id serial PRIMARY KEY,
  name varchar(100) NOT NULL,
  display_order integer NOT NULL DEFAULT 0,
  created_at integer NOT NULL DEFAULT 0,
  updated_at integer NOT NULL DEFAULT 0
);

COMMENT ON TABLE categories IS 'カテゴリマスタ';
COMMENT ON COLUMN categories.id IS 'カテゴリID';
COMMENT ON COLUMN categories.name IS 'カテゴリ名';
COMMENT ON COLUMN categories.display_order IS '表示順';
COMMENT ON COLUMN categories.created_at IS '作成日時';
COMMENT ON COLUMN categories.updated_at IS '更新日時';

CREATE INDEX IF NOT EXISTS idx_categories_display_order ON categories (display_order);

INSERT INTO categories (id, name, display_order, created_at, updated_at) VALUES
(1, '家具', 1, EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer, EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer),
(2, '家電', 2, EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer, EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer),
(3, '食品', 3, EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer, EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer)
ON CONFLICT (id) DO UPDATE SET
  name = EXCLUDED.name,
  display_order = EXCLUDED.display_order,
  updated_at = EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)::integer;

SELECT setval(pg_get_serial_sequence('categories', 'id'), COALESCE((SELECT MAX(id) FROM categories), 1));

-- ============================================================
-- 3. products（商品テーブル）
-- ============================================================

CREATE TABLE IF NOT EXISTS products (
  id serial PRIMARY KEY,
  product_code varchar(50) NOT NULL,
  name varchar(255) NOT NULL,
  price integer NOT NULL DEFAULT 0,
  description text DEFAULT NULL,
  image_url varchar(500) DEFAULT NULL,
  stock_quantity integer NOT NULL DEFAULT 0,
  category_id integer DEFAULT NULL,
  status smallint NOT NULL DEFAULT 1,
  review numeric(2,1) NOT NULL DEFAULT 0.0,
  release_date date DEFAULT NULL,
  created_at integer NOT NULL DEFAULT 0,
  updated_at integer NOT NULL DEFAULT 0,
  UNIQUE (product_code)
);

COMMENT ON TABLE products IS '商品テーブル';
COMMENT ON COLUMN products.id IS '商品ID';
COMMENT ON COLUMN products.product_code IS '商品コード';
COMMENT ON COLUMN products.name IS '商品名';
COMMENT ON COLUMN products.price IS '価格（税込）';
COMMENT ON COLUMN products.description IS '商品説明';
COMMENT ON COLUMN products.image_url IS '商品画像URL';
COMMENT ON COLUMN products.stock_quantity IS '在庫数';
COMMENT ON COLUMN products.category_id IS 'カテゴリID（将来の拡張用）';
COMMENT ON COLUMN products.status IS 'ステータス（1:販売中, 0:販売停止）';
COMMENT ON COLUMN products.review IS '評価（0.0～5.0）';
COMMENT ON COLUMN products.release_date IS '発売日';
COMMENT ON COLUMN products.created_at IS '作成日時';
COMMENT ON COLUMN products.updated_at IS '更新日時';

CREATE INDEX IF NOT EXISTS idx_products_category_id ON products (category_id);
CREATE INDEX IF NOT EXISTS idx_products_status ON products (status);
CREATE INDEX IF NOT EXISTS idx_products_created_at ON products (created_at);

INSERT INTO products (id, product_code, name, price, description, image_url, stock_quantity, category_id, status, review, release_date, created_at, updated_at) VALUES
(1, 'PROD001', 'サンプル商品1', 1000, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 10, 1, 1, 0.0, '2025-01-01', 1769406292, 1769406292),
(2, 'PROD002', 'サンプル商品2', 2000, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 5, 1, 1, 0.5, '2018-11-01', 1769406292, 1769406292),
(3, 'PROD003', 'サンプル商品3', 3000, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 20, 1, 1, 1.0, '2026-02-01', 1769406292, 1769406292),
(4, 'PROD004', 'サンプル商品4', 1500, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 0, 2, 1, 2.5, '2026-01-12', 1769406292, 1769406292),
(5, 'PROD005', 'サンプル商品5', 2500, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 15, 2, 1, 4.0, '2022-09-01', 1769406292, 1769406292),
(6, 'PROD006', 'サンプル商品6', 2500, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 15, 2, 1, 4.5, '2021-04-23', 1769406292, 1769406292),
(7, 'PROD007', 'サンプル商品7', 2500, '親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。小使に負ぶさって帰って来た時、おやじが大きな眼をして二階ぐらいから飛び降りて腰を抜かす奴があるかと云ったから、この次は抜かさずに飛んで見せますと答えた。', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEj7QEZuxLo_zvFCMKLbMyH5pFU-LihRIeLXMY-QHxEMIgeNOVhvKeSMiNsIxrzNFHMsUv0nxYYl_b5RVqLtJcRgJokPMn8IVpkRDKfnrMr1dsoghyXHGXRakLCV1wX0FBGlltS5W34zMGV4/s1600/no_image_square.jpg', 15, 3, 1, 5.0, '2024-06-11', 1769406292, 1769406292)
ON CONFLICT (id) DO UPDATE SET updated_at = EXCLUDED.updated_at;

SELECT setval(pg_get_serial_sequence('products', 'id'), COALESCE((SELECT MAX(id) FROM products), 1));

-- ============================================================
-- 4. cart（カートテーブル）
-- ============================================================

CREATE TABLE IF NOT EXISTS cart (
  id serial PRIMARY KEY,
  user_id integer NOT NULL,
  product_id integer NOT NULL,
  quantity integer NOT NULL DEFAULT 1,
  add_date date NOT NULL,
  UNIQUE (user_id, product_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

COMMENT ON TABLE cart IS 'カート';
COMMENT ON COLUMN cart.id IS 'カートID';
COMMENT ON COLUMN cart.user_id IS 'ユーザーID（users.id）';
COMMENT ON COLUMN cart.product_id IS '商品ID（products.id）';
COMMENT ON COLUMN cart.quantity IS '数量';
COMMENT ON COLUMN cart.add_date IS '追加日（YYYY-MM-DD）';

CREATE INDEX IF NOT EXISTS idx_cart_user_id ON cart (user_id);
CREATE INDEX IF NOT EXISTS idx_cart_product_id ON cart (product_id);
