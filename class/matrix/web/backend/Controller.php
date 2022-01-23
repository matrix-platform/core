<?php //>

namespace matrix\web\backend;

use matrix\web\RequestHandler;
use matrix\web\Session;
use matrix\web\UserAuthenticator;

class Controller {

    use authority\Authorization, RequestHandler, Session, UserAuthenticator;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    public function execute() {
        if ($this->authenticate()) {
            $node = $this->menuNode();
            $menu = $this->permitted($node);

            if ($menu) {
                $this->menu($menu)->node($node)->handle();
            } else {
                $this->response()->status(403);
            }
        }
    }

    public function menuNode() {
        return preg_replace('/^\/backend\/(.+)$/', '$1', $this->name());
    }

}
