<?php //>

return new class('SystemLog') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns([
            'type',
            'create_time',
        ]);
    }

};
