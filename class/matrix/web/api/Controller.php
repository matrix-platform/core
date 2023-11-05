<?php //>

namespace matrix\web\api;

use Exception;
use matrix\web\RequestHandler;

class Controller {

    use RequestHandler;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    public function execute() {
        $this->response()->headers(['Access-Control-Allow-Headers' => '*', 'Access-Control-Allow-Origin' => '*']);

        if ($this->authenticate()) {
            $this->handle();
        }
    }

    public function verify() {
        return true;
    }

    protected function authenticate() {
        return true;
    }

    protected function get($name) {
        throw new Exception('Unsupported operation.');
    }

    protected function remove($name) {
        throw new Exception('Unsupported operation.');
    }

    protected function set($name, $value) {
        throw new Exception('Unsupported operation.');
    }

}
