<?php //>

return new class('SmsLog') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns([
            'receiver',
            'content',
            'ip',
            'create_time',
        ]);
    }

};
