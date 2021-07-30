<?php //>

namespace matrix\web\member;

trait RememberMe {

    protected function forget() {
        $token = $this->getToken();

        if ($token) {
            $this->removeToken();

            @unlink(APP_DATA . 'member/' . $token);
        }
    }

    protected function recall() {
        $token = $this->getToken();

        if ($token) {
            $file = APP_DATA . 'member/' . $token;
            $member = json_decode(@file_get_contents($file), true);

            if ($member) {
                unlink($file);

                $this->remember($member);

                return $member;
            } else {
                $this->removeToken();
            }
        }

        return null;
    }

    protected function remember($member) {
        $folder = create_folder(APP_DATA . 'member');

        if ($folder) {
            while (true) {
                $token = sha1(uniqid('', true));
                $file = "{$folder}/{$token}";

                if (!file_exists($file)) {
                    file_put_contents($file, json_encode($member));

                    $this->setToken($token);

                    break;
                }
            }
        }
    }

    private function getToken() {
        return @$_COOKIE['matrix-r'];
    }

    private function removeToken() {
        $this->setToken('0', -time());
    }

    private function setToken($value, $expires = 86400 * 365) {
        setcookie('matrix-r', $value, [
            'expires' => time() + $expires,
            'httponly' => true,
            'path' => APP_PATH,
            'samesite' => 'none',
            'secure' => true,
        ]);
    }

}
