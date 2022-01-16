<?php //>

namespace matrix\web;

class VendorController {

    use RequestHandler, Session, VendorAware;

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
            $this->response()->status(401);
        } else {
            $this->response()->redirect(APP_ROOT . 'vendor/login-form/' . base64_urlencode($_SERVER['REQUEST_URI']));
        }

        return false;
    }

}
