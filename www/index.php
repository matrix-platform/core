<?php //>

define('APP_HOME', dirname(__DIR__) . '/');

require APP_HOME . 'vendor/autoload.php';

define('MATRIX', APP_HOME . 'vendor/matrix-platform/core/');

require MATRIX . 'app.php';
