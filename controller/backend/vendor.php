<?php //>

return new class('Vendor') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns('username', 'name', 'mobile', 'mail', 'disabled');
    }

};
