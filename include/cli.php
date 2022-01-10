<?php //>

use Monolog\ErrorHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

require find_resource('include/ansi.php');

(new Run())->prependHandler(new PlainTextHandler())->register();

define('REMOTE_ADDR', null);

$path = @$_SERVER['argv'][1];
$method = PHP_SAPI;

ErrorHandler::register(logging('error'));
