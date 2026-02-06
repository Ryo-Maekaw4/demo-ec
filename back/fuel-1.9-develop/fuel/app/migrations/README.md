# SQL マイグレーション

このシステムで使うテーブルを定義した SQL ファイルです。**番号順に実行**してください。  
`fuel_dev` データベースの構造に合わせてあります。

## ファイル一覧

| ファイル | テーブル | 説明 |
|----------|----------|------|
| 001_users.sql | users | ユーザーテーブル。認証・ログインで使用。 |
| 002_categories.sql | categories | カテゴリマスタ。**products より先に実行すること。** |
| 003_products.sql | products | 商品マスタ。INSERT に category_id を含む（004 は統合済みで不要）。 |
| 004_cart.sql | cart | カート。users.id / products.id を参照。 |

## 実行方法

MySQL でデータベースを指定して**番号順**に実行します。

```bash
# 例: fuel_dev データベースに実行
mysql -u root -p fuel_dev < fuel/app/migrations/001_users.sql
mysql -u root -p fuel_dev < fuel/app/migrations/002_categories.sql
mysql -u root -p fuel_dev < fuel/app/migrations/003_products.sql
mysql -u root -p fuel_dev < fuel/app/migrations/004_cart.sql
```

または MySQL クライアントで:

```sql
USE fuel_dev;
SOURCE /path/to/fuel/app/migrations/001_users.sql;
SOURCE /path/to/fuel/app/migrations/002_categories.sql;
SOURCE /path/to/fuel/app/migrations/003_products.sql;
SOURCE /path/to/fuel/app/migrations/004_cart.sql;
```

## サンプルデータ

- **001_users.sql**  
  - users 構造とテスト用ユーザー 3 件（demo / admin / test_user）を INSERT します。

- **002_categories.sql**  
  - カテゴリマスタ（家具・家電・食品）を INSERT します。003 の商品 INSERT で category_id を参照するため、必ず先に実行してください。

- **003_products.sql**  
  - 商品サンプル 7 件を INSERT します。各行に category_id を含むため、別ファイルで category_id を UPDATE する必要はありません（旧 004 は廃止・統合済み）。

- **004_cart.sql**  
  - カートテーブルを作成します。users と products が存在する状態で実行してください。
