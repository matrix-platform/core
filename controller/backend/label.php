<?php //>

return new class() extends matrix\web\UserController {

    protected function init() {
        $this->view(cfg('backend.label'));
    }

};
