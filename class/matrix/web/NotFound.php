<?php //>

namespace matrix\web;

class NotFound {

    use Responsible;

    public function __construct($path, $method) {
    }

    public function execute() {
        $this->response()->status(404);
    }

}
