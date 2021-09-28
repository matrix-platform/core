<?php //>

namespace matrix\web\member;

use matrix\web\Controller;
use matrix\web\MemberAware;

class LoginForm extends Controller {

    use MemberAware;

    public function __construct() {
        $this->values = ['view' => cfg('frontend.login-form')];
    }

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/[\w-]+)?$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $args = $this->args();
        $path = $args ? base64_urldecode($args[0]) : APP_ROOT;

        $result = ['success' => true, 'path' => $path];

        if ($this->member()) {
            $result['view'] = '302.php';
        } else if ($args) {
            $this->set('RETURN_PATH', $path);
        }

        return $result;
    }

}
