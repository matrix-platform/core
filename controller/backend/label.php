<?php //>

return new class() extends matrix\web\backend\Controller {

    protected function init() {
        $this->view(cfg('backend.label'));
    }

};
