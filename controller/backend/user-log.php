<?php //>

return new class('UserLog') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('username', 'user.username');

        $this->columns('username', 'type', 'ip', 'create_time');
    }

    protected function preprocess($form) {
        if (USER_ID > 1) {
            $form[] = $this->table()->user_id->greaterThan(1);
        }

        return $form;
    }

};
