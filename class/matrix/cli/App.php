<?php //>

namespace matrix\cli;

use matrix\core\App as AbstractApp;

class App extends AbstractApp {

    protected function __construct() {
        $languages = cfg('system.languages');

        preg_match("/^(\/({$languages}))?(\/.*)?$/", @$_SERVER['argv'][1], $info);

        define('LANGUAGE', $info[2] ?? cfg('default.language'));
        define('LANGUAGES', preg_split('/\|/', $languages));

        $this->controller = $this->find(@$info[3], PHP_SAPI);

        if ($this->controller === null) {
            $this->controller = new Controller(['path' => @$info[3], 'view' => 'cli/not-found.php']);
        }
    }

}
