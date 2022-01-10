<?php //>

namespace matrix\web;

trait UserAuthenticator {

    use UserAware;

    protected function authenticate() {
        $user = $this->user();

        if ($user) {
            define('USER_ID', $user['id']);

            return true;
        }

        if (defined('AJAX')) {
            $this->response()->status(401);
        } else {
            $this->response()->redirect(APP_ROOT . 'backend/login/' . base64_urlencode($_SERVER['REQUEST_URI']));
        }

        return false;
    }

}
