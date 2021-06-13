<?php //>

namespace matrix\web;

class Controller {

    use RequestHandler;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'GET' && $this->name() === $this->path());
    }

    public function execute() {
        $this->handle();
    }

}
