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
 *
 */

return array(
    'default' => array(
        'connection' => array(
            'dsn'      => 'pgsql:host='.(getenv('SUPABASE_DB_HOST') ?: 'db.ulizmfrojltqbmucqjfz.supabase.co').';port='.(getenv('SUPABASE_DB_PORT') ?: '5432').';dbname='.(getenv('SUPABASE_DB_NAME') ?: 'postgres'),
            'username' => getenv('SUPABASE_DB_USER') ?: 'postgres',
            'password' => getenv('SUPABASE_DB_PASSWORD') ?: '',
        ),
    ),
);
