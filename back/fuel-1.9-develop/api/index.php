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
 *  CORS: クロスオリジン（Vue など別ドメイン）からの API 呼び出しを許可
 * -----------------------------------------------------------------------------
 * ブラウザは事前に OPTIONS（プリフライト）を送る。200 + CORS を返さないと
 * 本番の POST/GET がブロックされ CORS エラーになる。
 * REQUEST_METHOD が Vercel 経由で GET に変わる場合があるため、
 * Access-Control-Request-Method があればプリフライトとみなして即 200 を返す。
 */
$isPreflight = (php_sapi_name() !== 'cli')
    && (
        ($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS'
        || !empty($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])
    );
if ($isPreflight) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
    http_response_code(200);
    exit;
}

/**
 * -----------------------------------------------------------------------------
 *  Vercel 用: リクエスト URI の補正
 * -----------------------------------------------------------------------------
 * 全ルートが /api/index.php?_path=$1 に転送される。_path がある場合は常に
 * REQUEST_URI を _path の値（クエリなし）で上書きする（FuelPHP のルート一致のため）。
 * REQUEST_URI に ?_path=... が付いたままだとルートにマッチしないため。
 */
if (php_sapi_name() !== 'cli') {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $restoredPath = null;

    if (isset($_GET['_path'])) {
        $restoredPath = '/' . ltrim((string) $_GET['_path'], '/');
        $qs = $_GET;
        unset($qs['_path']);
        if ($qs !== []) {
            $restoredPath .= '?' . http_build_query($qs);
        }
    } elseif (preg_match('#[?&]_path=([^&]+)#', $uri, $m)) {
        $restoredPath = '/' . ltrim(rawurldecode($m[1]), '/');
    } else {
        $needFix = ($uri === '' || $uri === '/api/index.php' || strpos($uri, '/api/index.php') === 0);
        if ($needFix && isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
            $restoredPath = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $restoredPath .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
    }

    if ($restoredPath !== null) {
        $_SERVER['REQUEST_URI'] = $restoredPath;
        $pathOnly = strpos($restoredPath, '?') !== false
            ? substr($restoredPath, 0, strpos($restoredPath, '?'))
            : $restoredPath;
        $_SERVER['PATH_INFO'] = $pathOnly;
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

// CORS: クロスオリジン（Vue など）からの API 呼び出しでレスポンスを許可
$corsOrigin = $_SERVER['HTTP_ORIGIN'] ?? '*';
$response->set_header('Access-Control-Allow-Origin', $corsOrigin);
$response->set_header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
$response->set_header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

$response->send(true);
