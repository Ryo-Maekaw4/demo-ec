# JWTトークンベース認証のセットアップ

## 📦 必要な準備

### 1. JWTライブラリのインストール

FuelPHP側でJWTライブラリをインストールします：

```bash
cd 10_Vue/maekawa_vue_ec/back/fuel-1.9-develop
composer require firebase/php-jwt
```

### 2. 設定ファイルの確認

`fuel/app/config/jwt.php` が作成されていることを確認してください。

**重要**: 本番環境では、`secret_key` を必ず変更してください！

```php
'secret_key' => 'your-secret-key-change-this-in-production-' . md5(__FILE__),
```

## 🔧 実装内容

### FuelPHP側

1. **`Auth_Jwt` クラス** (`fuel/app/classes/auth/jwt.php`)
   - JWTトークンの発行・検証を行うヘルパークラス

2. **ログインAPI** (`fuel/app/classes/controller/api/login.php`)
   - ログイン成功時にJWTトークンを発行
   - レスポンスに `token` フィールドを含める

3. **ログイン状態確認API** (`action_status`)
   - JWTトークンからユーザー情報を取得

4. **基底コントローラー** (`Controller_Api_Base`)
   - 保護されたAPIでJWTトークンを検証
   - `$use_jwt = true` でJWT認証を有効化

### Vue側

1. **Axiosインターセプター** (`src/plugins/axios.ts`)
   - すべてのリクエストに `Authorization: Bearer <token>` ヘッダーを自動追加
   - `withCredentials` は不要（トークンベースなので）

2. **認証管理** (`src/composables/useAuth.ts`)
   - JWTトークンを `localStorage` に保存
   - ログイン時にトークンを保存
   - ログアウト時にトークンを削除

## 🚀 使い方

### 保護されたAPIエンドポイントの作成

```php
class Controller_Api_Mypage extends Controller_Api_Base
{
    protected $use_jwt = true; // JWT認証を有効化
    
    public function action_index()
    {
        // $this->current_user にユーザー情報が入っている
        $user = $this->current_user;
        
        $response = Response::forge();
        $response->set_header('Content-Type', 'application/json; charset=utf-8');
        $response->set_header('Access-Control-Allow-Origin', '*');
        $response->body(json_encode(array(
            'success' => true,
            'user' => $user
        )));
        return $response;
    }
}
```

### Vue側での使用

```typescript
import { useAuth } from '@/composables/useAuth'

const { login, logout, isLoggedIn, user } = useAuth()

// ログイン
const result = await login('user@example.com', 'password')

// ログアウト
await logout()
```

## 🔐 セキュリティ注意事項

1. **秘密鍵の管理**
   - 本番環境では必ず強力な秘密鍵を使用
   - 環境変数や設定ファイルから読み込むことを推奨

2. **HTTPSの使用**
   - 本番環境では必ずHTTPSを使用
   - トークンが盗聴されるリスクを軽減

3. **トークンの有効期限**
   - デフォルトは1時間
   - 必要に応じて `jwt.php` で調整

4. **トークンの保存場所**
   - 現在は `localStorage` を使用
   - XSS攻撃に弱いため、必要に応じて `httpOnly` クッキーを検討

## ✅ 動作確認

1. ログインAPIを呼び出してトークンを取得
2. トークンが `localStorage` に保存されていることを確認
3. 保護されたAPIを呼び出して認証が機能することを確認
4. トークンを削除して、認証エラー（401）が返ることを確認

## 🐛 トラブルシューティング

### JWTライブラリが見つからない

```
composer require firebase/php-jwt
```

### トークンが無効と判定される

- 秘密鍵が一致しているか確認
- トークンの有効期限が切れていないか確認
- `Authorization: Bearer <token>` の形式で送信されているか確認

### CORSエラー

- `Access-Control-Allow-Headers` に `Authorization` が含まれているか確認
