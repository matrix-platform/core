<?php //>

namespace matrix\web\vendor;

use matrix\web\Controller;
use matrix\web\VendorAware;

class LoginForm extends Controller {

    use VendorAware;

    public function __construct() {
        $this->values = ['view' => cfg('frontend.vendor-login-form')];
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
        $path = $args ? base64_urldecode($args[0]) : (APP_ROOT . 'vendor/');

        $result = ['success' => true, 'path' => $path];

        if ($this->vendor()) {
            $result['view'] = '302.php';
        }

        return $result;
    }

}
