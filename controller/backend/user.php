<?php //>

return new class('User') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->password->invisible(true);
    }

    protected function preprocess($form) {
        if (USER_ID > 1) {
            $form[] = $this->table()->id->greaterThan(1);
        }

        return $form;
    }

};
