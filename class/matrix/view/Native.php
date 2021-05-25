<?php //>

namespace matrix\view;

class Native {

    private $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function render($controller, $form, $result) {
        require find_resource("view/native/{$this->view}");
    }

}
