<?php
/**
 * API Base Controller
 * 
 * すべてのAPIコントローラーの基底クラス
 * 認証が必要なAPIはこのクラスを継承する
 * 
 * 認証方式:
 * - セッションベース: \Auth::check() を使用（デフォルト）
 * - トークンベース: JWTトークンを検証（$use_jwt = true の場合）
 */
class Controller_Api_Base extends Controller
{
	/**
	 * JWT認証を使用するかどうか
	 * trueにすると、セッションではなくJWTトークンで認証
	 */
	protected $use_jwt = false;
	
	/**
	 * 認証を必須とするかどうか
	 * false にすると未ログインでもアクセス可能（商品一覧・詳細など）
	 */
	protected $auth_required = true;
	
	/**
	 * 現在のユーザー情報（JWT認証時）
	 */
	protected $current_user = null;
	
	/**
	 * コントローラー実行前に認証チェック
	 * 未ログインの場合は401エラーを返す（$auth_required が true の場合のみ）
	 */
	public function before()
	{
		parent::before();
		
		// OPTIONSリクエスト（プリフライト）は認証不要
		if (Input::method() === 'OPTIONS') {
			return;
		}
		
		// 認証不要のAPI（商品一覧・詳細など）はスキップ
		if (!$this->auth_required) {
			return;
		}
		
		// JWT認証を使用する場合
		if ($this->use_jwt) {
			$token = \Auth_Jwt::get_token_from_header();
			if (!$token) {
				return $this->unauthorized_response('トークンが提供されていません');
			}
			
			$user = \Auth_Jwt::get_user_from_token($token);
			if (!$user) {
				return $this->unauthorized_response('無効なトークンです');
			}
			
			$this->current_user = $user;
			return;
		}
		
		// セッションベース認証（デフォルト）
		if (!\Auth::check()) {
			return $this->unauthorized_response('認証が必要です');
		}
	}
	
	/**
	 * 401 Unauthorized レスポンスを返す
	 */
	protected function unauthorized_response($message = '認証が必要です')
	{
		$response = Response::forge();
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_status(401); // 401 Unauthorized
		$response->body(json_encode(array(
			'success' => false,
			'message' => $message,
			'error' => 'Unauthorized'
		)));
		return $response;
	}
	
	/**
	 * CORSヘッダーを設定する共通メソッド
	 */
	protected function set_cors_headers($response, $methods = 'GET, POST, PUT, DELETE, OPTIONS')
	{
		$response->set_header('Content-Type', 'application/json; charset=utf-8');
		$response->set_header('Access-Control-Allow-Origin', '*');
		$response->set_header('Access-Control-Allow-Methods', $methods);
		$response->set_header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
		$response->set_header('Access-Control-Allow-Credentials', 'true');
		return $response;
	}
}
