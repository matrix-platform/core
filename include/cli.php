<?php //>

use Monolog\ErrorHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

require 'ansi.php';

(new Run())->prependHandler(new PlainTextHandler())->register();

ErrorHandler::register(logger('error'));

matrix\cli\App::init();
