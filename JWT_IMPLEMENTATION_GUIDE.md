# JWTトークンベース認証への移行ガイド

## 📋 概要

現在の実装（セッションベース）から、**トークンベース（JWT）認証**への移行手順です。

## 🎯 なぜトークンベースが王道か？

### SPA + API構成ではトークンベースが標準

1. **スケーラビリティ**: ステートレスなので、複数のサーバーで負荷分散が容易
2. **CORS対応**: クッキーよりも扱いやすい
3. **モバイル対応**: モバイルアプリとの統合が簡単
4. **マイクロサービス**: 複数のAPIサービス間での認証情報の共有が容易

## 📦 必要な準備

### 1. FuelPHP側: JWTライブラリのインストール

```bash
cd 10_Vue/maekawa_vue_ec/back/fuel-1.9-develop
composer require firebase/php-jwt
```

### 2. 設定ファイルの作成

`fuel/app/config/jwt.php` を作成:

```php
<?php
return array(
    'secret_key' => 'your-secret-key-change-this-in-production',
    'expiration' => 3600, // 1時間（秒）
    'algorithm' => 'HS256',
);
```

## 🔧 実装手順

### Step 1: ログインAPIでJWTトークンを発行

`fuel/app/classes/controller/api/login.php` の `action_login()` を修正:

```php
// ログイン成功時
if (\Auth::instance()->login($login_identifier, $password)) {
    // ユーザー情報を取得
    $user_data = array(
        'id' => \Auth::get_user_id(),
        'username' => \Auth::get('username'),
        'email' => \Auth::get('email'),
        'name' => \Auth::get('name'),
    );
    
    // JWTトークンを発行
    $token = \Auth_Jwt::encode($user_data);
    
    $response->body(json_encode(array(
        'success' => true,
        'message' => 'ログインに成功しました',
        'token' => $token,  // トークンを返す
        'user' => $user_data
    )));
    return $response;
}
```

### Step 2: Vue側でトークンを保存・送信

`src/plugins/axios.ts` を修正:

```typescript
// リクエストインターセプター
apiClient.interceptors.request.use(
  (config) => {
    // localStorageからトークンを取得してAuthorizationヘッダーに追加
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)
```

`src/composables/useAuth.ts` を修正:

```typescript
// ログイン処理
const login = async (email: string, password: string, rememberMe: boolean = false) => {
  try {
    const response = await apiClient.post('/api/login/login', {
      email,
      password,
      remember_me: rememberMe
    })
    
    if (response.data.success) {
      // トークンをlocalStorageに保存
      if (response.data.token) {
        localStorage.setItem('auth_token', response.data.token)
      }
      
      isLoggedIn.value = true
      user.value = response.data.user || null
      saveAuthState()
      return { success: true, user: user.value }
    }
    // ...
  }
}

// ログアウト処理
const logout = async () => {
  try {
    await apiClient.post('/api/login/logout')
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    // トークンを削除
    localStorage.removeItem('auth_token')
    clearAuthState()
  }
}
```

### Step 3: 保護されたAPIでJWT認証を使用

`Controller_Api_Base` を継承するコントローラーで:

```php
class Controller_Api_Mypage extends Controller_Api_Base
{
    protected $use_jwt = true; // JWT認証を使用
    
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

## 🔄 現在の実装との比較

| 項目 | セッションベース（現在） | トークンベース（推奨） |
|------|------------------------|----------------------|
| **実装の複雑さ** | ⭐ 簡単 | ⭐⭐ やや複雑 |
| **スケーラビリティ** | ⭐⭐ 中程度 | ⭐⭐⭐ 高い |
| **CORS対応** | ⭐⭐ 設定必要 | ⭐⭐⭐ 簡単 |
| **モバイル対応** | ⭐⭐ やや困難 | ⭐⭐⭐ 容易 |
| **セキュリティ** | ⭐⭐⭐ 高い | ⭐⭐⭐ 高い（適切に実装すれば） |

## ✅ 推奨事項

**SPA + API構成では、トークンベース（JWT）を推奨します。**

理由:
- 業界標準のアプローチ
- スケーラビリティが高い
- モバイルアプリとの統合が容易
- マイクロサービスアーキテクチャに対応

ただし、**小規模なプロジェクトで、セッションベースで問題ない場合は、そのままでもOK**です。

## 🚀 移行のタイミング

1. **新規プロジェクト**: 最初からトークンベースで実装
2. **既存プロジェクト**: 
   - セッションベースで問題なければ維持
   - スケールが必要になったら移行
   - モバイルアプリを追加する際に移行
