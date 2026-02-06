<?php
/**
 * JWT認証ヘルパークラス
 * 
 * JWTトークンの発行・検証を行う
 * 使用するには: composer require firebase/php-jwt
 */
class Auth_Jwt
{
	/**
	 * JWTの秘密鍵を取得（設定ファイルから読み込み）
	 */
	private static function get_secret_key()
	{
		return \Config::get('jwt.secret_key', 'your-secret-key-change-this-in-production');
	}
	
	/**
	 * トークンの有効期限を取得（設定ファイルから読み込み）
	 */
	private static function get_expiration()
	{
		return \Config::get('jwt.expiration', 3600);
	}
	
	/**
	 * JWTトークンを発行
	 * 
	 * @param array $payload トークンに含めるデータ（ユーザーID、メールアドレスなど）
	 * @return string JWTトークン
	 */
	public static function encode($payload)
	{
		// firebase/php-jwtライブラリが必要
		if (!class_exists('Firebase\JWT\JWT')) {
			throw new Exception('firebase/php-jwtライブラリがインストールされていません。composer require firebase/php-jwt を実行してください。');
		}
		
		$issued_at = time();
		$expiration_time = $issued_at + self::get_expiration();
		
		$token_payload = array(
			'iat' => $issued_at,        // 発行時刻
			'exp' => $expiration_time,  // 有効期限
			'data' => $payload          // ユーザーデータ
		);
		
		return \Firebase\JWT\JWT::encode($token_payload, self::get_secret_key(), 'HS256');
	}
	
	/**
	 * JWTトークンを検証・デコード
	 * 
	 * @param string $token JWTトークン
	 * @return object|false デコードされたデータ、失敗時はfalse
	 */
	public static function decode($token)
	{
		if (!class_exists('Firebase\JWT\JWT')) {
			throw new Exception('firebase/php-jwtライブラリがインストールされていません。');
		}
		
		try {
			$decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(self::get_secret_key(), 'HS256'));
			return $decoded;
		} catch (\Exception $e) {
			// トークンが無効、期限切れ、改ざんされている場合
			return false;
		}
	}
	
	/**
	 * リクエストヘッダーからJWTトークンを取得
	 * 
	 * @return string|false トークン、見つからない場合はfalse
	 */
	public static function get_token_from_header()
	{
		// Authorization: Bearer <token> の形式を想定
		$headers = getallheaders();
		
		if (isset($headers['Authorization'])) {
			$auth_header = $headers['Authorization'];
			if (preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
				return $matches[1];
			}
		}
		
		// フォールバック: Input::headers()を使用
		$auth_header = Input::headers('Authorization', '');
		if (!empty($auth_header) && preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
			return $matches[1];
		}
		
		return false;
	}
	
	/**
	 * トークンからユーザー情報を取得
	 * 
	 * @param string $token JWTトークン
	 * @return array|false ユーザー情報、失敗時はfalse
	 */
	public static function get_user_from_token($token)
	{
		$decoded = self::decode($token);
		if ($decoded && isset($decoded->data)) {
			return (array)$decoded->data;
		}
		return false;
	}
}
