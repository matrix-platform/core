<?php //>

use matrix\cli\NotFound as CommandNotFound;
use matrix\web\NotFound as PageNotFound;
use Monolog\ErrorHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require 'include/functions.php';
require APP_HOME . 'config.php';

$folders = [];

if (defined('CUSTOM_APP')) {
    $folders['custom'] = APP_HOME . 'custom/' . CUSTOM_APP . '/';
}

$folders['base'] = APP_HOME;

foreach (PACKAGES as $package) {
    $folders[$package] = APP_HOME . 'vendor/' . $package . '/';
}

$folders['core'] = MATRIX;

define('RESOURCE_FOLDERS', $folders);

spl_autoload_register(function ($name) {
    $file = find_resource('class/' . str_replace('\\', '/', $name) . '.php');

    if ($file !== false) {
        isolate_require($file);
    }
}, true, true);

define('APP_PATH', preg_replace('/^\/?(.*\/)?[^\/]+$/', '/$1', $_SERVER['SCRIPT_NAME']));

if (PHP_SAPI === 'cli') {
    (new Run())->prependHandler(new PlainTextHandler())->register();

    require 'include/ansi.php';

    define('REMOTE_ADDR', null);

    $path = @$_SERVER['argv'][1];
    $method = PHP_SAPI;
} else {
    if (strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        define('AJAX', true);
    }

    if (cfg('system.debug')) {
        if (defined('AJAX')) {
            $handler = new JsonResponseHandler();
            $handler->addTraceToOutput(true);
        } else {
            $handler = new PrettyPageHandler();
        }

        (new Run())->prependHandler($handler)->register();
    }

    if (session_id() === '') {
        session_name('matrix');
        session_set_cookie_params(['httponly' => true, 'path' => APP_PATH, 'samesite' => 'none', 'secure' => true]);
        session_start();
    }

    if (!key_exists('WEBP', $_SESSION)) {
        if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') === false) {
            define('NO_WEBP', true);
        } else {
            $_SESSION['WEBP'] = true;
        }
    }

    define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

    $path = $_SERVER['PATH_INFO'];
    $method = $_SERVER['REQUEST_METHOD'];
}

ErrorHandler::register(logger('error'));

$languages = cfg('system.languages');

preg_match("/^(\/({$languages}))?(\/.*)?$/", $path, $info, PREG_UNMATCHED_AS_NULL);

if (@$info[2]) {
    define('APP_ROOT', APP_PATH . "{$info[2]}/");
    define('LANGUAGE', $info[2]);
} else {
    define('APP_ROOT', APP_PATH);
    define('LANGUAGE', cfg('default.language'));
}

define('LANGUAGES', preg_split('/\|/', $languages));
define('MULTILINGUAL', count(LANGUAGES) > 1);

$path = @$info[3] ?: '/';
$controller = route($path, $method);

if ($controller) {
    define('CONTROLLER', $controller->name());
} else {
    $controller = REMOTE_ADDR ? new PageNotFound($path, $method) : new CommandNotFound($path);
}

$controller->execute();
