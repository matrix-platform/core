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
            header('HTTP/1.1 401 Unauthorized');
        } else {
            $path = base64_urlencode($_SERVER['REQUEST_URI']);

            header('Location: ' . APP_ROOT . 'backend/login/' . $path);
        }

        return false;
    }

}
