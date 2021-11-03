<?php //>

return new class('VendorLog') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('username', 'vendor.username');

        $this->columns('username', 'type', 'ip', 'create_time');
    }

};
