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
 *  Database settings for development environment（Supabase）
 * -----------------------------------------------------------------------------
 *
 *  グローバル db.php の Supabase 設定をそのまま使用。
 *  Vercel では FUEL_ENV 未設定で development になるため、ここでも Vercel 時は
 *  Connection Pooler(6543) を使うようにする。
 *
 */

$host = getenv('SUPABASE_DB_HOST') ?: 'db.ulizmfrojltqbmucqjfz.supabase.co';
$port = getenv('SUPABASE_DB_PORT') ?: (getenv('VERCEL') ? '6543' : '5432');
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
