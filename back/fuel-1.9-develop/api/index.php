<?php
/**
 * FuelPHP Vercel エントリポイント
 *
 * Vercel のサーバーレス環境で FuelPHP を動作させるための api/index.php
 * 参考: https://qiita.com/Masanarea_qiita/items/2e1616e4e18f6c8ee26d
 *
 * @package    Fuel
 * @version    1.9-dev
 */

/**
 * -----------------------------------------------------------------------------
 *  Configure PHP Settings
 * -----------------------------------------------------------------------------
 */
error_reporting(-1);
ini_set('display_errors', 1);

/**
 * -----------------------------------------------------------------------------
 *  Vercel 用: リクエスト URI の補正
 * -----------------------------------------------------------------------------
 * 全ルートが /api/index.php?_path=$1 に転送されるため、元のパスを _path から
 * 復元して REQUEST_URI に設定する（FuelPHP のルーティングのため）
 */
if (php_sapi_name() !== 'cli') {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $needFix = ($uri === '' || $uri === '/api/index.php' || strpos($uri, '/api/index.php') === 0);
    if ($needFix && isset($_GET['_path'])) {
        $_SERVER['REQUEST_URI'] = '/' . ltrim((string) $_GET['_path'], '/');
        $qs = $_GET;
        unset($qs['_path']);
        if ($qs !== []) {
            $_SERVER['REQUEST_URI'] .= '?' . http_build_query($qs);
        }
    } elseif ($needFix && isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
}

/**
 * -----------------------------------------------------------------------------
 *  Define constants（api/ からの相対パス）
 * -----------------------------------------------------------------------------
 */
$base = dirname(__DIR__) . DIRECTORY_SEPARATOR;

define('DOCROOT', $base . 'public' . DIRECTORY_SEPARATOR);
define('APPPATH', realpath($base . 'fuel' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define('PKGPATH', realpath($base . 'fuel' . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define('COREPATH', realpath($base . 'fuel' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

/**
 * -----------------------------------------------------------------------------
 *  Check for dependencies
 * -----------------------------------------------------------------------------
 */
if (!file_exists(COREPATH . 'classes' . DIRECTORY_SEPARATOR . 'autoloader.php')) {
    http_response_code(500);
    die('No composer autoloader found. Please run composer install.');
}

require COREPATH . 'classes' . DIRECTORY_SEPARATOR . 'autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

/**
 * -----------------------------------------------------------------------------
 *  Route processing closure
 * -----------------------------------------------------------------------------
 */
$routerequest = function ($request = '', $e = false) {
    Request::reset_request(true);

    $route = array_key_exists($request, Router::$routes) ? Router::$routes[$request]->translation : Config::get('routes.' . $request);

    if ($route instanceof Closure) {
        $response = $route();
        if (!$response instanceof Response) {
            $response = Response::forge($response);
        }
    } elseif ($e === false) {
        $response = Request::forge()->execute()->response();
    } elseif ($route) {
        $response = Request::forge($route, false)->execute(array($e))->response();
    } elseif ($request) {
        $response = Request::forge($request)->execute(array($e))->response();
    } else {
        throw $e;
    }

    return $response;
};

/**
 * -----------------------------------------------------------------------------
 *  Start the Application
 * -----------------------------------------------------------------------------
 */
try {
    require APPPATH . 'bootstrap.php';
    $response = $routerequest();
} catch (HttpBadRequestException $e) {
    $response = $routerequest('_400_', $e);
} catch (HttpNoAccessException $e) {
    $response = $routerequest('_403_', $e);
} catch (HttpNotFoundException $e) {
    $response = $routerequest('_404_', $e);
} catch (HttpServerErrorException $e) {
    $response = $routerequest('_500_', $e);
}

$response->body((string) $response);

if (strpos($response->body(), '{exec_time}') !== false || strpos($response->body(), '{mem_usage}') !== false) {
    $bm = Profiler::app_total();
    $response->body(
        str_replace(
            array('{exec_time}', '{mem_usage}'),
            array(round($bm[0], 4), round($bm[1] / pow(1024, 2), 3)),
            $response->body()
        )
    );
}

$response->send(true);
