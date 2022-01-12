<?php //>

use matrix\cli\NotFound as CommandNotFound;
use matrix\web\NotFound as PageNotFound;

require 'include/functions.php';
require APP_HOME . 'config.php';

$folders = [];

if (defined('CUSTOM_APP')) {
    $folders['custom'] = APP_HOME . 'custom/' . CUSTOM_APP . '/';
}

$folders['base'] = APP_HOME;

foreach (PACKAGES as $package) {
    $folders[$package] = VENDOR_HOME . $package . '/';
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

require find_resource(PHP_SAPI === 'cli' ? 'include/cli.php' : 'include/web.php');

$languages = cfg('system.languages');

preg_match("/^(\/({$languages}))?(\/.*)?$/", $path, $info, PREG_UNMATCHED_AS_NULL);

if ($info[2]) {
    define('APP_ROOT', APP_PATH . "{$info[2]}/");
    define('LANGUAGE', $info[2]);
} else {
    define('APP_ROOT', APP_PATH);
    define('LANGUAGE', cfg('default.language'));
}

define('LANGUAGES', preg_split('/\|/', $languages));
define('MULTILINGUAL', count(LANGUAGES) > 1);

if ($info[3]) {
    $folder = cfg('backend.folder');
    $path = $folder ? preg_replace("/^\/{$folder}(\/.*)?$/", '/backend$1', $info[3]) : $info[3];
} else {
    $path = '/';
}

$controller = routing($path, $method);

if ($controller) {
    define('CONTROLLER', $controller->name());
} else {
    $controller = REMOTE_ADDR ? new PageNotFound($path, $method) : new CommandNotFound($path);
}

ob_start();

$controller->execute();

return $controller->response()->content(ob_get_clean())->send();
