<?php
/**
 * API Product Model
 * productsテーブルを参照するモデル
 */
class Model_Api_Product extends Model_Crud
{
	protected static $_table_name = 'products';
	protected static $_primary_key = 'id';
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
	
	/**
	 * テーブルカラム定義
	 */
	protected static $_properties = array(
		'id' => array(
			'type' => 'int',
			'label' => '商品ID',
		),
		'product_code' => array(
			'type' => 'string',
			'label' => '商品コード',
		),
		'name' => array(
			'type' => 'string',
			'label' => '商品名',
		),
		'price' => array(
			'type' => 'int',
			'label' => '価格',
		),
		'description' => array(
			'type' => 'string',
			'label' => '商品説明',
		),
		'image_url' => array(
			'type' => 'string',
			'label' => '商品画像URL',
		),
		'stock_quantity' => array(
			'type' => 'int',
			'label' => '在庫数',
		),
		'category_id' => array(
			'type' => 'int',
			'label' => 'カテゴリID',
			'null' => true,
		),
		'status' => array(
			'type' => 'int',
			'label' => 'ステータス',
		),
		'created_at' => array(
			'type' => 'int',
			'label' => '作成日時',
		),
		'updated_at' => array(
			'type' => 'int',
			'label' => '更新日時',
		),
	);
	
	/**
	 * 販売中の商品のみ取得
	 * FuelPHP Model_Crud::find は第1引数が config 配列のみ
	 */
	public static function find_active($where = array(), $order_by = array('created_at' => 'DESC'))
	{
		$where[] = array('status', '=', 1);
		return static::find(array(
			'where' => $where,
			'order_by' => $order_by,
		));
	}
	
	/**
	 * 商品コードで検索（1件または null）
	 * FuelPHP Model_Crud::find は第1引数が config 配列のみ
	 */
	public static function find_by_code($product_code)
	{
		$result = static::find(array(
			'where' => array(
				array('product_code', '=', $product_code),
				array('status', '=', 1),
			),
			'limit' => 1,
		));
		return (is_array($result) && count($result) > 0) ? $result[0] : null;
	}
	
	/**
	 * 商品名で検索（部分一致）。category_id を指定するとカテゴリで絞り込み。
	 */
	public static function search_by_name($keyword, $limit = 20, $offset = 0, $category_id = 0)
	{
		$query = \DB::select()
			->from(static::$_table_name)
			->where('status', '=', 1);
		
		if (!empty($keyword)) {
			$query->where('name', 'LIKE', '%' . $keyword . '%');
		}
		if ($category_id > 0) {
			$query->where('category_id', '=', $category_id);
		}
		
		$query->order_by('created_at', 'DESC')
			->limit($limit)
			->offset($offset);
		
		$results = $query->execute()->as_array();
		
		$products = array();
		foreach ($results as $result) {
			$product = static::forge($result);
			$products[] = $product;
		}
		
		return $products;
	}
	
	/**
	 * 検索結果の総数を取得。category_id を指定するとカテゴリで絞り込み。
	 */
	public static function count_search($keyword = '', $category_id = 0)
	{
		$query = \DB::select(\DB::expr('COUNT(*) as count'))
			->from(static::$_table_name)
			->where('status', '=', 1);
		
		if (!empty($keyword)) {
			$query->where('name', 'LIKE', '%' . $keyword . '%');
		}
		if (!empty($category_id)) {
			$query->where('category_id', '=', $category_id);
		}
		
		$result = $query->execute()->current();
		return (int)$result['count'];
	}
	
	/**
	 * 新着順（created_at の新しい順）で商品一覧を取得。category_id を指定するとカテゴリで絞り込み。
	 */
	public static function list_by_newest($limit = 20, $offset = 0, $category_id = 0)
	{
		$query = \DB::select()
			->from(static::$_table_name)
			->where('status', '=', 1);
		if ($category_id > 0) {
			$query->where('category_id', '=', $category_id);
		}
		$query->order_by('release_date', 'DESC')
			->limit($limit)
			->offset($offset);
		
		$results = $query->execute()->as_array();
		$products = array();
		foreach ($results as $result) {
			$products[] = static::forge($result);
		}
		return $products;
	}
	
	/**
	 * 人気順（評価・レビュー高い順）で商品一覧を取得。category_id を指定するとカテゴリで絞り込み。
	 * review カラムでソート（NULL は 0 扱い）、同順は created_at DESC
	 */
	public static function list_by_popular($limit = 20, $offset = 0, $category_id = 0)
	{
		$query = \DB::select()
			->from(static::$_table_name)
			->where('status', '=', 1);
		if ($category_id > 0) {
			$query->where('category_id', '=', $category_id);
		}
		$query->order_by(\DB::expr('COALESCE(review, 0)'), 'DESC')
			->order_by('created_at', 'DESC')
			->limit($limit)
			->offset($offset);
		
		$results = $query->execute()->as_array();
		$products = array();
		foreach ($results as $result) {
			$products[] = static::forge($result);
		}
		return $products;
	}
	
	/**
	 * 販売中商品の総数。category_id を指定するとカテゴリで絞り込み。
	 */
	public static function count_all($category_id = 0)
	{
		$query = \DB::select(\DB::expr('COUNT(*) as count'))
			->from(static::$_table_name)
			->where('status', '=', 1);
		if ($category_id > 0) {
			$query->where('category_id', '=', $category_id);
		}
		$result = $query->execute()->current();
		return (int)$result['count'];
	}
}
