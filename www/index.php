<?php //>

define('APP_HOME', dirname(__DIR__) . '/');
define('VENDOR_HOME', APP_HOME . 'vendor/');

require VENDOR_HOME . 'autoload.php';

define('MATRIX', VENDOR_HOME . 'matrix-platform/core/');

require MATRIX . 'app.php';
