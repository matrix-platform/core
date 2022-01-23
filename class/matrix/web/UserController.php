<?php //>

namespace matrix\web;

class UserController {

    use RequestHandler, Session, UserAuthenticator;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    public function execute() {
        if ($this->authenticate()) {
            $this->handle();
        }
    }

}
