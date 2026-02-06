<?php
/**
 * API Account Model
 * accountテーブルを参照するモデル
 */
class Model_Api_Account extends Model_Crud
{
	protected static $_table_name = 'account';
	protected static $_primary_key = 'id';
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'updated_at';
}