<?php //>

namespace matrix\web\backend;

use matrix\db\Model;
use matrix\web\RequestHandler;
use matrix\web\UserAuthenticator;

class Controller {

    use Authorization, RequestHandler, UserAuthenticator;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    public function execute() {
        Model::enableAdministration();

        if ($this->authenticate()) {
            $node = $this->menuNode();
            $menu = $this->permitted($node);

            if ($menu) {
                $this->menu($menu)->node($node)->handle();
            } else {
                header('HTTP/1.1 403 Forbidden');
            }
        }
    }

    protected function menuNode() {
        return preg_replace('/^\/backend\/(.+)$/', '$1', $this->name());
    }

}
