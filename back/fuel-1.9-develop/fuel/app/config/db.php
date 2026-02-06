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
 *
 *  Vercel では db.xxx.supabase.co が IPv6 のため接続失敗することがあります。
 *  → Dashboard > Connect > Transaction mode の接続文字列から、
 *    SUPABASE_DB_POOLER_HOST（例: aws-0-ap-northeast-1.pooler.supabase.com）を
 *    設定すると Pooler 経由（IPv4 対応）で接続します。
 *
 */

$default_host = 'db.ulizmfrojltqbmucqjfz.supabase.co';
$default_ref = 'ulizmfrojltqbmucqjfz';

$pooler_host = getenv('SUPABASE_DB_POOLER_HOST');
if ($pooler_host !== false && $pooler_host !== '') {
    // Pooler 利用（Vercel 等 IPv4 必須環境向け）: ホスト・ポート・ユーザー形式を変更
    $supabase_host = $pooler_host;
    $supabase_port = getenv('SUPABASE_DB_PORT') ?: '6543';
    $project_ref   = getenv('SUPABASE_PROJECT_REF') ?: $default_ref;
    $supabase_user = 'postgres.' . $project_ref; // Pooler はこの形式必須
} else {
    $supabase_host = getenv('SUPABASE_DB_HOST') ?: $default_host;
    $supabase_port = getenv('SUPABASE_DB_PORT') ?: (getenv('VERCEL') ? '6543' : '5432');
    $supabase_user = getenv('SUPABASE_DB_USER') ?: 'postgres';
}
$supabase_db   = getenv('SUPABASE_DB_NAME') ?: 'postgres';
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
