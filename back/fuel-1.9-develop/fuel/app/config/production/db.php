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

/**
 * -----------------------------------------------------------------------------
 *  Database settings for production environment（Supabase）
 * -----------------------------------------------------------------------------
 *
 *  Vercel 等では環境変数 SUPABASE_DB_PASSWORD を設定してください。
 *  サーバーレスでは直接接続(5432)で "Cannot assign requested address" が出るため、
 *  本番は Connection Pooler（ポート 6543）をデフォルトにしています。
 *
 */

$host = getenv('SUPABASE_DB_HOST') ?: 'db.ulizmfrojltqbmucqjfz.supabase.co';
$port = getenv('SUPABASE_DB_PORT') ?: '6543'; // 本番は Pooler 推奨
$name = getenv('SUPABASE_DB_NAME') ?: 'postgres';

return array(
    'default' => array(
        'connection' => array(
            'dsn'      => 'pgsql:host='.$host.';port='.$port.';dbname='.$name,
            'username' => getenv('SUPABASE_DB_USER') ?: 'postgres',
            'password' => getenv('SUPABASE_DB_PASSWORD') ?: '',
        ),
    ),
);
