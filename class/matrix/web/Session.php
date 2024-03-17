<?php //>

namespace matrix\web;

trait Session {

    protected function get($name) {
        return $this->initSession() ? @$_SESSION[$name] : null;
    }

    protected function remove($name) {
        if ($this->initSession() && key_exists($name, $_SESSION)) {
            unset($_SESSION[$name]);
        }
    }

    protected function set($name, $value) {
        if ($this->initSession(true)) {
            $_SESSION[$name] = $value;
        }
    }

    private function initSession($force = false) {
        if (session_id()) {
            return true;
        }

        $exists = key_exists('matrix', $_COOKIE);

        if ($exists || $force) {
            if (!$exists) {
                $params = ['httponly' => true, 'path' => APP_PATH];

                if (defined('HTTPS')) {
                    $params['samesite'] = 'none';
                    $params['secure'] = true;
                }

                session_set_cookie_params($params);
            }

            session_name('matrix');

            return session_start();
        }

        return false;
    }

}
