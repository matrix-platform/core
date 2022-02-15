<?php //>

return new class('MailLog') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns([
            'receiver',
            'subject',
            'ip',
            'create_time',
            'status',
        ]);
    }

};
