<?php //>

namespace matrix\core;

use Exception;

class AppException extends Exception {

    private $error;
    private $extra;

    public function __construct($error, $message = '', $extra = null) {
        parent::__construct($message);

        $this->error = $error;
        $this->extra = $extra;
    }

    public function getError() {
        return $this->error;
    }

    public function getExtra() {
        return $this->extra;
    }

}
