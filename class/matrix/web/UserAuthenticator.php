<?php //>

namespace matrix\web;

use matrix\db\Model;

trait UserAuthenticator {

    use UserAware;

    protected function authenticate() {
        $user = $this->user();

        if ($user) {
            Model::enableAdministration();

            define('USER_ID', $user['id']);
            define('USER_LEVEL', USER_ID > 1000 ? 3 : (USER_ID > 1 ? 2 : 1));

            return true;
        }

        if (defined('AJAX')) {
            $this->response()->status(401);
        } else {
            $this->response()->redirect(APP_ROOT . (cfg('backend.folder') ?: 'backend') . '/login/' . base64_urlencode($_SERVER['REQUEST_URI']));
        }

        return false;
    }

}
