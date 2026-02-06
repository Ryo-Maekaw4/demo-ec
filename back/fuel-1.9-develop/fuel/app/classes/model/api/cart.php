<?php
/**
 * API Cart Model
 * cartテーブルを参照するモデル
 */
class Model_Api_Cart extends Model_Crud
{
	protected static $_table_name = 'cart';
	protected static $_primary_key = 'id';

	protected static $_properties = array(
		'id' => array('type' => 'int', 'label' => 'カートID'),
		'user_id' => array('type' => 'int', 'label' => 'ユーザーID'),
		'product_id' => array('type' => 'int', 'label' => '商品ID'),
		'quantity' => array('type' => 'int', 'label' => '数量'),
		'add_date' => array('type' => 'string', 'label' => '追加日'),
	);

	/**
	 * カートに商品を追加（既に同じ商品があれば数量を加算）
	 *
	 * @param int $user_id ユーザーID
	 * @param int $product_id 商品ID
	 * @param int $quantity 追加数量
	 * @return bool 成功時true
	 */
	public static function add_item($user_id, $product_id, $quantity)
	{
		$user_id = (int)$user_id;
		$product_id = (int)$product_id;
		$quantity = max(1, (int)$quantity);
		$today = date('Y-m-d');

		$existing = \DB::select()->from('cart')
			->where('user_id', $user_id)
			->where('product_id', $product_id)
			->execute()
			->current();

		if ($existing) {
			\DB::update('cart')
				->set(array(
					'quantity' => \DB::expr('`quantity` + ' . $quantity),
					'add_date' => $today,
				))
				->where('user_id', $user_id)
				->where('product_id', $product_id)
				->execute();
		} else {
			\DB::insert('cart')
				->set(array(
					'user_id' => $user_id,
					'product_id' => $product_id,
					'quantity' => $quantity,
					'add_date' => $today,
				))
				->execute();
		}
		return true;
	}

	/**
	 * ユーザーのカート一覧を取得（商品情報を JOIN）
	 *
	 * @param int $user_id ユーザーID
	 * @return array カート行（cart.id, product_id, quantity, add_date, 商品名・価格・画像など）
	 */
	public static function list_by_user($user_id)
	{
		$user_id = (int)$user_id;
		$rows = \DB::select(
			array('cart.id', 'cart_id'),
			array('cart.product_id', 'product_id'),
			array('cart.quantity', 'quantity'),
			array('cart.add_date', 'add_date'),
			array('products.name', 'product_name'),
			array('products.price', 'price'),
			array('products.image_url', 'image_url'),
			array('products.product_code', 'product_code'),
			array('products.stock_quantity', 'stock_quantity')
		)
			->from('cart')
			->join('products', 'INNER')->on('cart.product_id', '=', 'products.id')
			->where('cart.user_id', $user_id)
			->order_by('cart.add_date', 'DESC')
			->order_by('cart.id', 'ASC')
			->execute()
			->as_array();
		return $rows;
	}

	/**
	 * ユーザーのカート内「商品点数」の合計（数量の合計）
	 *
	 * @param int $user_id ユーザーID
	 * @return int 数量の合計
	 */
	public static function count_by_user($user_id)
	{
		$user_id = (int)$user_id;
		$row = \DB::select(\DB::expr('COALESCE(SUM(quantity), 0) as total'))
			->from('cart')
			->where('user_id', $user_id)
			->execute()
			->current();
		return (int)$row['total'];
	}
}
