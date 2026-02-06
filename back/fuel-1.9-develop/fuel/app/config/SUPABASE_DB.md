# Supabase データベース接続（FuelPHP）

## 接続情報

- **プロジェクト URL（API）**: https://ulizmfrojltqbmucqjfz.supabase.co
- **プロジェクト ID**: ulizmfrojltqbmucqjfz
- **DB ホスト**: `db.ulizmfrojltqbmucqjfz.supabase.co`
- **ポート**: 5432（直接接続・ローカル向け） / **6543（Connection pooler・Vercel 等サーバーレス向け）**
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
| `SUPABASE_DB_PORT` | 省略時はローカル 5432 / 本番(production) 6543 | 任意 |
| `SUPABASE_DB_NAME` | 省略時は `postgres` | 任意 |
| `SUPABASE_DB_USER` | 省略時は `postgres`（Pooler 利用時は `postgres.プロジェクトREF`） | 任意 |
| `SUPABASE_DB_POOLER_HOST` | **Vercel 用** Pooler ホスト（IPv4 対応）。下記「Pooler 接続」を参照 | 任意 |
| `SUPABASE_PROJECT_REF` | Pooler 利用時のプロジェクト REF。省略時は `ulizmfrojltqbmucqjfz` | 任意 |

## Vercel で接続できない場合（Cannot assign requested address / IPv6）

Vercel は **IPv4 のみ** のため、`db.xxx.supabase.co`（IPv6）では接続に失敗することがあります。**Pooler 用ホスト**（IPv4 対応）を使うと解消します。

### Pooler 接続の設定手順

1. [Supabase Dashboard](https://supabase.com/dashboard) でプロジェクトを開く
2. 左メニュー **Project Settings** → **Database**、または **Connect** ボタンを開く
3. **Connection string** で **Transaction**（または **Session**）を選択
4. 表示される接続文字列の **ホスト部分** をコピー  
   例: `postgres://postgres.ulizmfrojltqbmucqjfz:[PASSWORD]@aws-0-ap-northeast-1.pooler.supabase.com:6543/postgres`  
   → ホストは `aws-0-ap-northeast-1.pooler.supabase.com`
5. Vercel の環境変数に追加:
   - `SUPABASE_DB_POOLER_HOST` = 上記ホスト（例: `aws-0-ap-northeast-1.pooler.supabase.com`）
   - `SUPABASE_PROJECT_REF` = プロジェクト REF（例: `ulizmfrojltqbmucqjfz`）。接続文字列のユーザー `postgres.XXXXX` の XXXXX 部分
6. API プロジェクトを再デプロイ

Pooler 利用時はユーザー名が自動で `postgres.プロジェクトREF` になります。`SUPABASE_DB_PASSWORD` はこれまで通り必要です。

## テーブル作成

初回は `fuel/app/migrations/schema_postgres.sql` を Supabase の SQL Editor で実行するか、`psql` で流してテーブルを作成してください。
