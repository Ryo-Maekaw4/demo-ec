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
 *  Vercel 等では SUPABASE_DB_POOLER_HOST を設定して Pooler（IPv4）で接続してください。
 *
 */

$default_host = 'db.ulizmfrojltqbmucqjfz.supabase.co';
$default_ref = 'ulizmfrojltqbmucqjfz';
$pooler_host  = getenv('SUPABASE_DB_POOLER_HOST');

if ($pooler_host !== false && $pooler_host !== '') {
    $host = $pooler_host;
    $port = getenv('SUPABASE_DB_PORT') ?: '6543';
    $ref  = getenv('SUPABASE_PROJECT_REF') ?: $default_ref;
    $user = 'postgres.' . $ref; // Pooler はこの形式必須
} else {
    $host = getenv('SUPABASE_DB_HOST') ?: $default_host;
    $port = getenv('SUPABASE_DB_PORT') ?: '6543';
    $user = getenv('SUPABASE_DB_USER') ?: 'postgres';
}
$name = getenv('SUPABASE_DB_NAME') ?: 'postgres';

return array(
    'default' => array(
        'connection' => array(
            'dsn'      => 'pgsql:host='.$host.';port='.$port.';dbname='.$name,
            'username' => $user,
            'password' => getenv('SUPABASE_DB_PASSWORD') ?: '',
        ),
    ),
);
