<?php //>

define('APP_HOME', dirname(__DIR__) . '/');

require APP_HOME . 'vendor/autoload.php';

matrix\core\App::getInstance()->run();
