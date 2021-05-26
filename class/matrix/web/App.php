<?php //>

namespace matrix\web;

use matrix\core\App as AbstractApp;

class App extends AbstractApp {

    protected function __construct() {
        if (session_id() === '') {
            if (defined('APP_NAME')) {
                session_name(APP_NAME);
            }

            session_start();
        }

        define('APP_PATH', preg_replace('/^(.*\/)[^\/]+$/', '$1', $_SERVER['SCRIPT_NAME']));

        $languages = cfg('system.languages');

        preg_match("/^(\/({$languages}))?(\/.*)?$/", $_SERVER['PATH_INFO'], $info);

        if ($info[2]) {
            define('APP_ROOT', APP_PATH . "{$info[2]}/");
            define('LANGUAGE', $info[2]);
        } else {
            define('APP_ROOT', APP_PATH);
            define('LANGUAGE', cfg('default.language'));
        }

        define('LANGUAGES', preg_split('/\|/', $languages));
        define('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

        $path = $info[3] ?? '/';

        $this->controller = $this->find($path, $_SERVER['REQUEST_METHOD']);

        if ($this->controller === null) {
            $this->controller = new Controller(['path' => $path, 'view' => 'web/404.php']);
        }
    }

}
