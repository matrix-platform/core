<?php //>

return new class('UserLog') extends matrix\web\backend\ListController {

    protected function preprocess($form) {
        if (USER_ID > 1) {
            $form[] = $this->table()->user_id->greaterThan(1);
        }

        return $form;
    }

};
