<?php
/**
 * The Contact Model (Topic7-2: データベース版)
 *
 * @package  app
 */
class Model_Sample_Contact_Db extends Model_Crud
{
	// テーブル名を指定
	protected static $_table_name = 'contacts';
	
	// 主キーを指定
	protected static $_primary_key = 'id';
	
	// タイムスタンプを自動更新する
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
}
