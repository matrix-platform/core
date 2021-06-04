<?php //>

namespace matrix\view;

use Exception;

class Native {

    private $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function render($controller, $form, $result) {
        $file = find_resource("view/native/{$this->view}");

        if ($file === false) {
            throw new Exception("View `{$this->view}` not found.");
        }

        require $file;
    }

}
