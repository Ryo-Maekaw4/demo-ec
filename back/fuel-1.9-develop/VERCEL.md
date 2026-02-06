# FuelPHP API を Vercel にデプロイする手順

[Laravel10をVercelに簡単にデプロイ！](https://qiita.com/Masanarea_qiita/items/2e1616e4e18f6c8ee26d) を参考に、FuelPHP API を Vercel で動かすための設定です。

## 前提

- 本リポジトリの **ルートを `back/fuel-1.9-develop` にした状態** で Vercel にデプロイするか、Vercel の「Root Directory」で `back/fuel-1.9-develop` を指定してください。
- `composer install` が実行されるようにしてください（Vercel の「Install Command」で `composer install` を指定）。

## 1. 用意されているファイル

| ファイル | 役割 |
|----------|------|
| `api/index.php` | Vercel サーバーレス用のエントリポイント。FuelPHP をブートストラップし、全リクエストを処理する。 |
| `vercel.json` | Vercel の関数・ルーティング設定。`vercel-php@0.9.0`（Node.js 22.x / PHP 8.5）を使用し、全パスを `api/index.php` に転送する。 |

## 2. Vercel での設定

### 2.1 プロジェクトのインポート

1. [Vercel](https://vercel.com) にログインし、Git リポジトリをインポートする。
2. **Root Directory** に `back/fuel-1.9-develop` を指定する（リポジトリ直下がルートの場合は空でよい）。

### 2.2 ビルド・インストール設定

- **Framework Preset**: 指定しない、または **Other** を選択。
- **Install Command**: `composer install` を指定（FuelPHP の `fuel/core` 等をインストールするため）。
- **Build Command**: 未指定でよい。
- **Output Directory**: 未指定でよい。

### 2.3 環境変数

Laravel の記事と同様に、必要な値を Vercel の「Environment Variables」に設定してください。

| 変数名 | 説明 |
|--------|------|
| `FUEL_ENV` | `production` 推奨（本番時）。 |
| （DB 利用時） | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` など、`.env` や `config/db.php` で参照している変数。 |

その他、JWT やアプリ固有の設定も必要に応じて追加してください。

## 3. デプロイ

- 上記の通り Root Directory と Install Command を設定したうえで **Deploy** を実行する。
- デプロイ後、表示された URL（例: `https://xxxx.vercel.app`）で API にアクセスできる。

## 4. ルーティングについて

- `vercel.json` の `routes` で `/(.*)` を `/api/index.php` に転送しているため、  
  `https://xxxx.vercel.app/api/login` や `https://xxxx.vercel.app/api/product/list` など、  
  `config/routes.php` で定義したパスがそのまま利用できます。
- 元のリクエストパスは、Vercel の PHP ランタイムにより `$_SERVER['REQUEST_URI']` に渡る想定です。  
  もしルーティングが期待どおりでない場合は、`api/index.php` 内の `REQUEST_URI` 補正部分を確認してください。

## 5. トラブルシューティング

### ブラウザや Vue から API を叩くと「404 NOT_FOUND」（Code: NOT_FOUND、ID: hnd1::...）

これは **FuelPHP の 404 ではなく、Vercel の 404** です。  
＝ サーバーレス関数 `api/index.php` に届く前に、Vercel が「関数が見つからない」と判断しています。

**原因**: リポジトリルート（`demo-ec/`）のままデプロイしており、Vercel がルート直下の `api/index.php` を探しているが、実際のファイルは `back/fuel-1.9-develop/api/index.php` にあるため。

**対処**:

1. Vercel Dashboard → 対象プロジェクト → **Settings** → **General**
2. **Root Directory** を **Edit** し、`back/fuel-1.9-develop` を指定して **Save**
3. **Deployments** タブで最新のデプロイの **⋮** → **Redeploy** を実行

Root Directory を設定して再デプロイすると、`api/index.php` が正しく認識され、`/api/login/status` や `/api/product/list` などにアクセスできるようになります。

## 6. 参考リンク

- [Laravel10をVercelに簡単にデプロイ！外部公開までの手順](https://qiita.com/Masanarea_qiita/items/2e1616e4e18f6c8ee26d)
- [vercel-community/php](https://github.com/vercel-community/php)（Vercel 用 PHP ランタイム）
