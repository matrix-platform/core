<?php //>

use Monolog\ErrorHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

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

if (@$_SERVER['REVERSE_PROXY'] && $_SERVER['REVERSE_PROXY'] === $_SERVER['REMOTE_ADDR']) {
    $addresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    define('REMOTE_ADDR', trim(end($addresses)));
} else {
    define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
}

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

ErrorHandler::register(logging('error'));
