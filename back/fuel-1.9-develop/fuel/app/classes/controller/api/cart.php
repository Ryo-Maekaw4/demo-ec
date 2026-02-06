<?php
/**
 * API Cart Controller
 * カート追加API（JWT認証必須）
 */
class Controller_Api_Cart extends Controller_Api_Base
{
	protected $use_jwt = true;
	protected $auth_required = true;

	/**
	 * カートに追加
	 * POST /api/cart/add（body: product_id, quantity）
	 */
	public function action_add()
	{
		$response = Response::forge();
		$this->set_cors_headers($response, 'POST, OPTIONS');

		if (Input::method() === 'OPTIONS') {
			return $response;
		}

		$user = $this->current_user;
		if (!$user || !isset($user['id'])) {
			$response->set_status(401);
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'ログインが必要です',
			)));
			return $response;
		}

		$post_data = $this->_get_post_data();
		$product_id = isset($post_data['product_id']) ? (int)$post_data['product_id'] : 0;
		$quantity = isset($post_data['quantity']) ? (int)$post_data['quantity'] : 1;

		if ($product_id <= 0) {
			$response->set_status(400);
			$response->body(json_encode(array(
				'success' => false,
				'message' => '商品IDが不正です',
			)));
			return $response;
		}

		$quantity = max(1, min(999, $quantity));

		// 商品存在・在庫チェック（任意）
		$product = \DB::select()->from('products')->where('id', $product_id)->execute()->current();
		if (!$product) {
			$response->set_status(404);
			$response->body(json_encode(array(
				'success' => false,
				'message' => '商品が見つかりません',
			)));
			return $response;
		}

		try {
			Model_Api_Cart::add_item($user['id'], $product_id, $quantity);
			$response->body(json_encode(array(
				'success' => true,
				'message' => 'カートに追加しました',
			)));
		} catch (\Exception $e) {
			\Log::error('Cart add error: ' . $e->getMessage());
			\Log::error($e->getTraceAsString());
			$response->set_status(500);
			// 原因特定のため例外メッセージを返す（本番では debug を外すか null にすること）
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'カートの追加に失敗しました',
				'debug' => $e->getMessage(),
			)));
		}
		return $response;
	}

	/**
	 * カート一覧取得
	 * POST /api/cart/list（body 不要。JWT から user_id を取得）
	 */
	public function action_list()
	{
		$response = Response::forge();
		$this->set_cors_headers($response, 'POST, OPTIONS');

		if (Input::method() === 'OPTIONS') {
			return $response;
		}

		$user = $this->current_user;
		if (!$user || !isset($user['id'])) {
			$response->set_status(401);
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'ログインが必要です',
			)));
			return $response;
		}

		try {
			$items = Model_Api_Cart::list_by_user($user['id']);
			$list = array();
			foreach ($items as $row) {
				$list[] = array(
					'cart_id' => (int)$row['cart_id'],
					'product_id' => (int)$row['product_id'],
					'quantity' => (int)$row['quantity'],
					'add_date' => $row['add_date'],
					'product_name' => $row['product_name'],
					'price' => (int)$row['price'],
					'image_url' => $row['image_url'],
					'product_code' => $row['product_code'],
					'stock_quantity' => (int)$row['stock_quantity'],
					'subtotal' => (int)$row['price'] * (int)$row['quantity'],
				);
			}
			$total = array_sum(array_column($list, 'subtotal'));
			$response->body(json_encode(array(
				'success' => true,
				'items' => $list,
				'total' => $total,
			)));
		} catch (\Exception $e) {
			\Log::error('Cart list error: ' . $e->getMessage());
			$response->set_status(500);
			$response->body(json_encode(array(
				'success' => false,
				'message' => 'カートの取得に失敗しました',
			)));
		}
		return $response;
	}

	/**
	 * カート件数取得（バッジ表示用）
	 * POST /api/cart/count（body 不要。JWT から user_id を取得）
	 */
	public function action_count()
	{
		$response = Response::forge();
		$this->set_cors_headers($response, 'POST, OPTIONS');

		if (Input::method() === 'OPTIONS') {
			return $response;
		}

		$user = $this->current_user;
		if (!$user || !isset($user['id'])) {
			$response->set_status(401);
			$response->body(json_encode(array(
				'success' => false,
				'count' => 0,
			)));
			return $response;
		}

		try {
			$count = Model_Api_Cart::count_by_user($user['id']);
			$response->body(json_encode(array(
				'success' => true,
				'count' => (int)$count,
			)));
		} catch (\Exception $e) {
			$response->set_status(500);
			$response->body(json_encode(array(
				'success' => false,
				'count' => 0,
			)));
		}
		return $response;
	}

	/**
	 * POST パラメータ取得（JSON または form）
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
