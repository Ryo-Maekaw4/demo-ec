<?php
/**
 * JWT Configuration
 * 
 * JWTトークンの設定
 */
return array(
	/**
	 * JWTの秘密鍵
	 * 本番環境では必ず変更してください！
	 * 環境変数から取得することを推奨
	 */
	'secret_key' => 'your-secret-key-change-this-in-production-' . md5(__FILE__),
	
	/**
	 * トークンの有効期限（秒）
	 * デフォルト: 1時間（3600秒）
	 */
	'expiration' => 3600,
	
	/**
	 * リフレッシュトークンの有効期限（秒）
	 * デフォルト: 30日（2592000秒）
	 * 未実装の場合は無視されます
	 */
	'refresh_expiration' => 2592000,
	
	/**
	 * 使用するアルゴリズム
	 * HS256: 対称鍵（シンプル、推奨）
	 * RS256: 非対称鍵（より安全だが複雑）
	 */
	'algorithm' => 'HS256',
);
