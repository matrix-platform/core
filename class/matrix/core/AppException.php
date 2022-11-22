<?php //>

namespace matrix\core;

use Exception;

class AppException extends Exception {

    private $error;

    public function __construct($error, $message = '') {
        parent::__construct($message);

        $this->error = $error;
    }

    public function getError() {
        return $this->error;
    }

}
