<?php //>

namespace matrix\web;

trait RememberMe {

    protected function forget() {
        $token = $this->getToken();

        if ($token) {
            $this->removeToken();

            @unlink(APP_DATA . $this->getTokenName() . '/' . $token);

            return true;
        }

        return false;
    }

    abstract protected function getTokenName();

    protected function recall() {
        $token = $this->getToken();

        if ($token) {
            $file = APP_DATA . $this->getTokenName() . '/' . $token;
            $who = json_decode(@file_get_contents($file), true);

            if ($who) {
                unlink($file);

                $this->remember($who);

                return $who;
            } else {
                $this->removeToken();
            }
        }

        return null;
    }

    protected function remember($who) {
        $folder = create_folder(APP_DATA . $this->getTokenName());

        if ($folder) {
            while (true) {
                $token = sha1(uniqid('', true));
                $file = "{$folder}/{$token}";

                if (!file_exists($file)) {
                    file_put_contents($file, json_encode($who));

                    $this->setToken($token);

                    break;
                }
            }
        }
    }

    private function getToken() {
        return @$_COOKIE[$this->getTokenName()];
    }

    private function removeToken() {
        $this->setToken('0', -time());
    }

    private function setToken($value, $expires = 86400 * 365) {
        setcookie($this->getTokenName(), $value, [
            'expires' => time() + $expires,
            'httponly' => true,
            'path' => APP_PATH,
            'samesite' => 'none',
            'secure' => true,
        ]);
    }

}
