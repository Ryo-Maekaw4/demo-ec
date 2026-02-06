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
 *  Vercel 用に SUPABASE_DB_POOLER_HOST を設定すると Pooler（IPv4）で接続します。
 *
 */

$default_host = 'db.ulizmfrojltqbmucqjfz.supabase.co';
$default_ref = 'ulizmfrojltqbmucqjfz';

$parse_host_port = function ($v) {
    if ($v === '' || strpos($v, '://') === false) {
        return array($v, null);
    }
    $p = parse_url($v);
    return array(isset($p['host']) ? $p['host'] : $v, isset($p['port']) ? (string) $p['port'] : null);
};

$pooler_host_raw = getenv('SUPABASE_DB_POOLER_HOST');
$pooler_host = ($pooler_host_raw !== false && $pooler_host_raw !== '') ? trim($pooler_host_raw) : '';

if ($pooler_host !== '') {
    list($host, $parsed_port) = $parse_host_port($pooler_host);
    $port = $parsed_port !== null ? $parsed_port : (getenv('SUPABASE_DB_PORT') ?: '6543');
    $ref  = getenv('SUPABASE_PROJECT_REF') ?: $default_ref;
    $user = 'postgres.' . $ref;
} else {
    $host_raw = getenv('SUPABASE_DB_HOST') ?: $default_host;
    list($host, $parsed_port) = $parse_host_port($host_raw);
    $port = $parsed_port !== null ? $parsed_port : (getenv('SUPABASE_DB_PORT') ?: (getenv('VERCEL') ? '6543' : '5432'));
    $user = (strpos($host, 'pooler.supabase.com') !== false)
        ? ('postgres.' . (getenv('SUPABASE_PROJECT_REF') ?: $default_ref))
        : (getenv('SUPABASE_DB_USER') ?: 'postgres');
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
