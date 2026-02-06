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
 *  Global database settings（Supabase / PostgreSQL）
 * -----------------------------------------------------------------------------
 *
 *  Supabase のデータベース接続用。
 *  パスワードは Supabase ダッシュボード > Project Settings > Database の
 *  "Database password" を環境変数 SUPABASE_DB_PASSWORD に設定してください。
 *  （Publishable key / Secret key は API 用で、DB 接続には使いません）
 *
 */

$supabase_host = getenv('SUPABASE_DB_HOST') ?: 'db.ulizmfrojltqbmucqjfz.supabase.co';
$supabase_port = getenv('SUPABASE_DB_PORT') ?: '5432';
$supabase_db   = getenv('SUPABASE_DB_NAME') ?: 'postgres';
$supabase_user = getenv('SUPABASE_DB_USER') ?: 'postgres';
$supabase_pass = getenv('SUPABASE_DB_PASSWORD') ?: '';

return array(
    'default' => array(
        'type' => 'pdo',
        'connection' => array(
            'dsn'        => 'pgsql:host='.$supabase_host.';port='.$supabase_port.';dbname='.$supabase_db,
            'username'   => $supabase_user,
            'password'   => $supabase_pass,
            'persistent' => false,
        ),
        'identifier'   => '"',
        'table_prefix' => '',
        'charset'      => 'utf8',
        'enable_cache' => true,
        'profiling'    => false,
        'readonly'     => false,
    ),
);
