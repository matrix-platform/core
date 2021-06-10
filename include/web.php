<?php //>

use Monolog\ErrorHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

define('TWIG_CACHE', true);

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

ErrorHandler::register(logger('error'));

matrix\web\App::init();
