<?php //>

define('MATRIX', dirname(__DIR__) . '/');

require 'functions.php';
require APP_HOME . 'config.php';

$folders = [];

if (defined('CUSTOM_APP')) {
    $folders['custom'] = APP_HOME . CUSTOM_APP . '/';
}

$folders['base'] = APP_HOME;

if (defined('PACKAGES')) {
    foreach (PACKAGES as $package) {
        $folders[$package] = APP_HOME . 'vendor/' . $package . '/';
    }
}

$folders['core'] = MATRIX;

define('RESOURCE_FOLDERS', $folders);

spl_autoload_register(function ($name) {
    $file = find_resource('class/' . str_replace('\\', '/', $name) . '.php');

    if ($file !== false) {
        isolate_require($file);
    }
}, true, true);
