<?php //>

namespace matrix\web\backend;

use matrix\web\UserController;

class Controller extends UserController {

    use Authorizer;

    protected function authorize() {
        if (parent::authorize()) {
            $node = $this->menuNode();
            $menu = $this->permitted($node);

            if ($menu) {
                $this->menu($menu)->node($node);

                return true;
            }

            header('HTTP/1.1 403 Forbidden');
        }

        return false;
    }

    protected function menuNode() {
        return preg_replace('/^\/backend\/(.+)$/', '$1', $this->name());
    }

}
