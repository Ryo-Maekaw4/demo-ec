<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010-2026 Fuel Development Team
 * @link       https://fuelphp.com
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Default route
	 * -------------------------------------------------------------------------
	 *
	 */

	'_root_' => 'welcome/index',

	/**
	 * -------------------------------------------------------------------------
	 *  Page not found
	 * -------------------------------------------------------------------------
	 *
	 */

	'_404_' => 'welcome/404',

	/**
	 * -------------------------------------------------------------------------
	 *  Example for Presenter
	 * -------------------------------------------------------------------------
	 *
	 *  A route for showing page using Presenter
	 *
	 */

	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	// ここがURLになる 例：http://localhost/fuel/education
	'education' => array('education/index'),
	'education/index' => array('education/index'),
	'education/topic1' => array('education/topic1'),
	'education/topic2' => array('education/topic2'),
	'education/topic3' => array('education/topic3'),
	'education/topic4' => array('education/topic4'),
	'education/topic5' => array('education/topic5'),
	'education/topic6' => array('education/topic6'),
	'education/topic7' => array('education/topic7'),
	'education/topic8' => array('education/topic8'),
	'education/topic9' => array('education/topic9'),
	'education/topic10' => array('education/topic10'),

	// Topic7-1: お問い合わせフォーム（セッション版）
	'sample/contact/session' => array('sample/contact/session/index'),
	'sample/contact/session/confirm' => array('sample/contact/session/confirm'),
	'sample/contact/session/complete' => array('sample/contact/session/complete'),

	// Topic7-2: お問い合わせフォーム（データベース版）
	'sample/contact/db' => array('sample/contact/db/index'),
	'sample/contact/db/confirm' => array('sample/contact/db/confirm'),
	'sample/contact/db/complete' => array('sample/contact/db/complete'),

	// API Routes
	'api/login' => array('api/login/index'),
	'api/login/status' => array('api/login/status'),
	'api/login/login' => array('api/login/login'),
	'api/login/logout' => array('api/login/logout'),
	'api/login/update' => array('api/login/update'),
	'api/login/change_password' => array('api/login/change_password'),
	
	// Product API Routes
	'api/product/list' => array('api/product/list'),
	'api/product/detail' => array('api/product/detail'),

	// Cart API Routes（JWT認証必須）
	'api/cart/add' => array('api/cart/add'),
	'api/cart/list' => array('api/cart/list'),
	'api/cart/count' => array('api/cart/count'),
);
