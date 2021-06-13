<?php //>

namespace matrix\web;

class NotFound {

    public function __construct($path, $method) {
    }

    public function execute() {
        header('HTTP/1.1 404 Not Found');
    }

}
