<?php
/**
 * API Product Controller
 * 商品一覧・検索API
 */
class Controller_Api_Product extends Controller_Api_Base
{
	/**
	 * JWT認証は不要（商品一覧・詳細は誰でも見られる）
	 */
	protected $use_jwt = false;
	
	/**
	 * 認証不要（未ログインでも商品一覧・詳細にアクセス可能）
	 */
	protected $auth_required = false;
	
	/**
	 * 商品一覧取得API
	 * POST /api/product/list（body: keyword, page, limit, sort, category）
	 */
	public function action_list()
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
		
		// パラメータ取得（POST body: JSON または form）
		$post_data = $this->_get_post_data();
		$keyword = isset($post_data['keyword']) ? (string)$post_data['keyword'] : '';
		$page = isset($post_data['page']) ? (int)$post_data['page'] : 1;
		$limit = isset($post_data['limit']) ? (int)$post_data['limit'] : 20;
		$sort = isset($post_data['sort']) ? (string)$post_data['sort'] : 'newest'; // newest | popular
		$category_id = isset($post_data['category']) ? (int)$post_data['category'] : 0;
		
		// ページネーション計算
		$offset = ($page - 1) * $limit;
		
		// 並び順に応じて取得（category_id で絞り込み）
		if ($sort === 'popular') {
			$products = Model_Api_Product::list_by_popular($limit, $offset, $category_id);
			$total = Model_Api_Product::count_all($category_id);
		} elseif ($sort === 'newest') {
			$products = Model_Api_Product::list_by_newest($limit, $offset, $category_id);
			$total = Model_Api_Product::count_all($category_id);
		} else {
			$products = Model_Api_Product::search_by_name($keyword, $limit, $offset, $category_id);
			$total = Model_Api_Product::count_search($keyword, $category_id);
		}
		
		// レスポンスデータ作成
		$product_list = array();
		foreach ($products as $product) {
			$product_list[] = array(
				'id' => $product->id,
				'product_code' => $product->product_code,
				'name' => $product->name,
				'price' => (int)$product->price,
				'description' => $product->description,
				'image_url' => $product->image_url,
				'stock_quantity' => (int)$product->stock_quantity,
				'is_in_stock' => (int)$product->stock_quantity > 0,
				'review' => $product->review,
				'release_date' => Date::forge($product->release_date)->format('%Y年%m月%d日'),
			);
		}
		
		$response->body(json_encode(array(
			'success' => true,
			'products' => $product_list,
			'pagination' => array(
				'page' => $page,
				'limit' => $limit,
				'total' => $total,
				'total_pages' => ceil($total / $limit),
			),
		)));
		
		return $response;
	}
	
	/**
	 * 商品詳細取得API
	 * POST /api/product/detail（body: id または code）
	 */
	public function action_detail()
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
		
		$product = null;
		
		// パラメータ取得（POST body: JSON または form）
		$post_data = $this->_get_post_data();
		$id = isset($post_data['id']) ? $post_data['id'] : null;
		if ($id !== null && $id !== '') {
			$id = (int)$id;
			$result = Model_Api_Product::find(array(
				'where' => array(
					array('id', '=', $id),
					array('status', '=', 1),
				),
				'limit' => 1,
			));
			$product = (is_array($result) && count($result) > 0) ? $result[0] : null;
		}
		
		// 商品コードで検索
		$code = isset($post_data['code']) ? $post_data['code'] : null;
		if (!$product && $code !== null && $code !== '') {
			$product = Model_Api_Product::find_by_code($code);
		}
		
		if (!$product) {
			$response->body(json_encode(array(
				'success' => false,
				'message' => '商品が見つかりません',
			)));
			return $response;
		}
		
		$response->body(json_encode(array(
			'success' => true,
			'product' => array(
				'id' => $product->id,
				'product_code' => $product->product_code,
				'name' => $product->name,
				'price' => (int)$product->price,
				'description' => $product->description,
				'image_url' => $product->image_url,
				'stock_quantity' => (int)$product->stock_quantity,
				'is_in_stock' => (int)$product->stock_quantity > 0,
				'review' => $product->review,
				'release_date' => $product->release_date,
			),
		)));
		
		return $response;
	}
	
	/**
	 * POST パラメータ取得（Content-Type: application/json の場合は php://input を JSON 解析）
	 */
	protected function _get_post_data()
	{
		$content_type = Input::server('HTTP_CONTENT_TYPE', Input::server('CONTENT_TYPE', ''));
		if (strpos($content_type, 'application/json') !== false) {
			$raw = file_get_contents('php://input');
			$decoded = json_decode($raw, true);
			return is_array($decoded) ? $decoded : array();
		}
		return Input::post();
	}
}
