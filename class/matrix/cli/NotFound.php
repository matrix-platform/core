<?php //>

namespace matrix\cli;

class NotFound {

    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function execute() {
        echo "Controller \"{$this->path}\" not found.\n";
    }

}
