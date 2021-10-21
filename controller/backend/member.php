<?php //>

return new class('Member') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns('username', 'name', 'mobile', 'mail', 'disabled');
    }

};
