<?php //>

use matrix\web\backend\authority\Authorization;

return new class() extends matrix\web\UserController {

    use Authorization;

    protected function init() {
        $this->view(cfg('backend.search-modal'));
    }

};
