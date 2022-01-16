<?php //>

namespace matrix\web;

use matrix\db\Model;

class UserController {

    use RequestHandler, Session, UserAuthenticator;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    public function execute() {
        Model::enableAdministration();

        if ($this->authenticate()) {
            $this->handle();
        }
    }

}
