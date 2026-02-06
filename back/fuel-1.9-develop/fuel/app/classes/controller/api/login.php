<?php
/**
 * API Login Controller
 * FuelPHP Authパッケージを使用したログインAPI
 * 参考: http://fuelphp.jp/docs/1.8/packages/auth/examples/auth.html
 */
class Controller_Api_Login extends Controller
{
	/**
	 * API動作確認用
	 */
	public function action_index()
	{
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->body(json_encode(array('success' => true, 'message' => 'Login API is working!')));
		return $response;
	}

	/**
	 * ログイン状態確認API
	 * JWTトークンからログイン状態を確認
	 */
	public function action_status()
	{
		// CORSヘッダーを設定
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_header('Access-Control-Allow-Methods', 'POST, OPTIONS');
		$response->set_header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
		
		// OPTIONSリクエストの処理（プリフライトリクエスト）
		if (Input::method() === 'OPTIONS') {
			return $response;
		}
		
		// JWTトークンを取得
		$token = \Auth_Jwt::get_token_from_header();
		
		if ($token) {
			// トークンからユーザー情報を取得
			$user = \Auth_Jwt::get_user_from_token($token);
			
			if ($user) {
				// トークンが有効
				$response->body(json_encode(array(
					'success' => true,
					'isLoggedIn' => true,
					'user' => $user
				)));
			} else {
				// トークンが無効または期限切れ
				$response->body(json_encode(array(
					'success' => true,
					'isLoggedIn' => false,
					'user' => null,
					'message' => 'トークンが無効または期限切れです'
				)));
			}
		} else {
			// トークンが提供されていない
			$response->body(json_encode(array(
				'success' => true,
				'isLoggedIn' => false,
				'user' => null
			)));
		}
		
		return $response;
	}

	/**
	 * ログイン処理
	 * Authパッケージを使用したログイン実装
	 */
	public function action_login()
	{
		// CORSヘッダーを設定
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_header('Access-Control-Allow-Methods', 'POST, OPTIONS');
		$response->set_header('Access-Control-Allow-Headers', 'Content-Type');
		
		// OPTIONSリクエストの処理（プリフライトリクエスト）
		if (Input::method() === 'OPTIONS') {
			return $response;
		}
		
		// すでにログイン済みかチェック
		// JWTトークンベースでは、既にログイン済みでもトークンを発行する必要がある
		if (\Auth::check()) {
			// ユーザー情報を取得
			$user_data = array(
				'id' => \Auth::get_user_id(),
				'username' => \Auth::get('username'),
				'email' => \Auth::get('email'),
				'name' => \Auth::get('name'),
			);
			
			// JWTトークンを発行（既にログイン済みでもトークンを発行）
			try {
				$token = \Auth_Jwt::encode($user_data);
				
				$response->body(json_encode(array(
					'success' => true,
					'message' => '既にログイン済みです',
					'token' => $token,  // JWTトークンを返す
					'user' => $user_data
				)));
				return $response;
			} catch (\Exception $e) {
				// JWTライブラリがインストールされていない場合
				$response->body(json_encode(array(
					'success' => false,
					'message' => '認証システムのエラーが発生しました: ' . $e->getMessage()
				)));
				return $response;
			}
		}
		
		// JSONボディからデータを取得
		$json_data = json_decode(file_get_contents('php://input'), true);
		
		// フォールバック: JSONが取得できない場合は通常のPOSTデータから取得
		if (empty($json_data)) {
			$username = Input::post('username', '');
			$email = Input::post('email', '');
			$password = Input::post('password', '');
			$remember = Input::post('remember', false) ?: Input::post('remember_me', false);
		} else {
			$username = isset($json_data['username']) ? $json_data['username'] : '';
			$email = isset($json_data['email']) ? $json_data['email'] : '';
			$password = isset($json_data['password']) ? $json_data['password'] : '';
			$remember = isset($json_data['remember']) ? $json_data['remember'] : 
			           (isset($json_data['remember_me']) ? $json_data['remember_me'] : false);
		}

		// バリデーション
		// パスワードは必須
		if (empty($password)) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'パスワードは必須です'
			)));
			return $response;
		}
		
		// usernameまたはemailのどちらか一方は必須
		if (empty($username) && empty($email)) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'ユーザー名またはメールアドレスのどちらか一方は必須です'
			)));
			return $response;
		}
		
		// usernameまたはemailのどちらかを使用（優先順位: username > email）
		$login_identifier = !empty($username) ? $username : $email;
		
		// Authパッケージを使用してログイン
		// Auth::instance()->login()は、usernameまたはemailのどちらでもログイン可能
		// simpleauth.phpの設定でaccountテーブルを使用するように設定済み
		if (\Auth::instance()->login($login_identifier, $password)) {
			// ユーザー情報を取得
			$user_data = array(
				'id' => \Auth::get_user_id(),
				'username' => \Auth::get('username'),
				'email' => \Auth::get('email'),
				'name' => \Auth::get('name'),
			);
			
			// JWTトークンを発行
			try {
				$token = \Auth_Jwt::encode($user_data);
				
				// ログイン成功（JWTトークンを含む）
				$response->body(json_encode(array(
					'success' => true,
					'message' => 'ログインに成功しました',
					'token' => $token,  // JWTトークンを返す
					'user' => $user_data
				)));
				return $response;
			} catch (\Exception $e) {
				// JWTライブラリがインストールされていない場合
				$response->body(json_encode(array(
					'success' => false,
					'message' => '認証システムのエラーが発生しました: ' . $e->getMessage()
				)));
				return $response;
			}
		} else {
			// ログイン失敗
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'ユーザー名（またはメールアドレス）またはパスワードが正しくありません'
			)));
			return $response;
		}
	}

	/**
	 * ログアウト処理
	 * JWTトークンベースでは、トークンを削除するだけでログアウト完了
	 * （ステートレスなので、サーバー側で特別な処理は不要）
	 */
	public function action_logout()
	{
		// CORSヘッダーを設定
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_header('Access-Control-Allow-Methods', 'POST, OPTIONS');
		$response->set_header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
		
		// OPTIONSリクエストの処理
		if (Input::method() === 'OPTIONS') {
			return $response;
		}
		
		// JWTトークンベースでは、クライアント側でトークンを削除するだけでOK
		// サーバー側では特に処理不要（ステートレス）
		$response->body(json_encode(array(
			'success' => true,
			'message' => 'ログアウトしました'
		)));
		return $response;
	}

	/**
	 * ユーザー情報更新API（JWT必須）
	 * POST /api/login/update（body: name, email）
	 */
	public function action_update()
	{
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_header('Access-Control-Allow-Methods', 'POST, OPTIONS');
		$response->set_header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

		if (Input::method() === 'OPTIONS') {
			return $response;
		}

		$token = \Auth_Jwt::get_token_from_header();
		if (!$token) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => '認証が必要です'
			)));
			return $response;
		}

		$user = \Auth_Jwt::get_user_from_token($token);
		if (!$user || empty($user['id'])) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'トークンが無効です'
			)));
			return $response;
		}

		$user_id = (int)$user['id'];

		$json_data = json_decode(file_get_contents('php://input'), true);
		if (!is_array($json_data)) {
			$json_data = array();
		}
		$name = isset($json_data['name']) ? trim((string)$json_data['name']) : null;
		$email = isset($json_data['email']) ? trim((string)$json_data['email']) : null;

		$updated = array();
		if ($name !== null) {
			$updated['name'] = $name;
		}
		if ($email !== null) {
			if ($email === '') {
				$response->body(json_encode(array(
					'success' => false,
					'message' => 'メールアドレスは必須です'
				)));
				return $response;
			}
			// 他ユーザーとの重複チェック（自分以外で同じemailがいるか）
			$existing = \DB::select()->from('account')
				->where('email', '=', $email)
				->where('id', '!=', $user_id)
				->execute()->current();
			if ($existing) {
				$response->body(json_encode(array(
					'success' => false,
					'message' => 'このメールアドレスは既に使用されています'
				)));
				return $response;
			}
			$updated['email'] = $email;
		}

		if (empty($updated)) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => '変更する項目（name または email）を指定してください'
			)));
			return $response;
		}

		$updated['updated_at'] = time();
		\DB::update('account')->set($updated)->where('id', '=', $user_id)->execute();

		$row = \DB::select()->from('account')->where('id', '=', $user_id)->execute()->current();
		$user_data = array(
			'id' => (int)$row['id'],
			'username' => $row['username'],
			'email' => $row['email'],
			'name' => $row['name'] !== null ? $row['name'] : '',
		);

		$response->body(json_encode(array(
			'success' => true,
			'message' => 'ユーザー情報を更新しました',
			'user' => $user_data
		)));
		return $response;
	}

	/**
	 * パスワード変更API（JWT必須）
	 * POST /api/login/change_password（body: new_password=新しいパスワード）
	 * JWTからログイン中ユーザーを特定し、そのユーザーのパスワードを更新
	 */
	public function action_change_password()
	{
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_header('Access-Control-Allow-Methods', 'POST, OPTIONS');
		$response->set_header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

		if (Input::method() === 'OPTIONS') {
			return $response;
		}

		$token = \Auth_Jwt::get_token_from_header();
		if (!$token) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => '認証が必要です'
			)));
			return $response;
		}

		$user = \Auth_Jwt::get_user_from_token($token);
		if (!$user || empty($user['id'])) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'トークンが無効です'
			)));
			return $response;
		}

		$user_id = (int)$user['id'];

		$json_data = json_decode(file_get_contents('php://input'), true);
		if (!is_array($json_data)) {
			$json_data = array();
		}
		$new_password = isset($json_data['new_password']) ? $json_data['new_password'] : '';

		if (strlen($new_password) < 6) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => '新しいパスワードは6文字以上で入力してください'
			)));
			return $response;
		}

		$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
		\DB::update('account')
			->set(array(
				'password' => $password_hash,
				'updated_at' => time(),
			))
			->where('id', '=', $user_id)
			->execute();

		$response->body(json_encode(array(
			'success' => true,
			'message' => 'パスワードを変更しました'
		)));
		return $response;
	}
}