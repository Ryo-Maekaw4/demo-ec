# Supabase データベース接続（FuelPHP）

## 接続情報

- **プロジェクト URL（API）**: https://ulizmfrojltqbmucqjfz.supabase.co
- **プロジェクト ID**: ulizmfrojltqbmucqjfz
- **DB ホスト**: `db.ulizmfrojltqbmucqjfz.supabase.co`
- **ポート**: 5432（直接接続） / 6543（Connection pooler 利用時）
- **データベース名**: postgres
- **ユーザー名**: postgres

## パスワードについて

**Publishable key / Secret key は API 用です。DB 接続には使いません。**

FuelPHP から Supabase の Postgres に接続するには、**データベースのパスワード**が必要です。

1. [Supabase Dashboard](https://supabase.com/dashboard) でプロジェクトを開く
2. **Project Settings** → **Database**
3. **Database password** を確認（プロジェクト作成時に設定したもの）。忘れた場合は **Reset database password** で再設定可能。

## 環境変数

ローカルや Vercel で、次の環境変数を設定してください。

| 変数名 | 説明 | 例 |
|--------|------|-----|
| `SUPABASE_DB_PASSWORD` | 上記「Database password」 | （必須） |
| `SUPABASE_DB_HOST` | 省略時は `db.ulizmfrojltqbmucqjfz.supabase.co` | 任意 |
| `SUPABASE_DB_PORT` | 省略時は `5432` | 任意 |
| `SUPABASE_DB_NAME` | 省略時は `postgres` | 任意 |
| `SUPABASE_DB_USER` | 省略時は `postgres` | 任意 |

## テーブル作成

初回は `fuel/app/migrations/schema_postgres.sql` を Supabase の SQL Editor で実行するか、`psql` で流してテーブルを作成してください。
