<?php //>

namespace matrix\web;

class VendorController {

    use RequestHandler, VendorAware;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'GET' && $this->name() === $this->path());
    }

    public function execute() {
        if ($this->authenticate()) {
            $this->handle();
        }
    }

    protected function authenticate() {
        $vendor = $this->vendor();

        if ($vendor) {
            define('VENDOR_ID', $vendor['id']);

            return true;
        }

        if (defined('AJAX')) {
            header('HTTP/1.1 401 Unauthorized');
        } else {
            $path = base64_urlencode($_SERVER['REQUEST_URI']);

            header('Location: ' . APP_ROOT . 'vendor/login-form/' . $path);
        }

        return false;
    }

}
