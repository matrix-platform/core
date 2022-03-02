<?php //>

return new class('UserLog') extends matrix\web\backend\GetController {

    protected function init() {
        $table = $this->table();

        $table->add('username', 'user.username');

        $table->user_id->invisible(true);
    }

    protected function postprocess($form, $result) {
        if (USER_ID > 1 && $result['data']['user_id'] === 1) {
            return [
                'view' => 'error.php',
                'error' => 'error.data-not-found',
            ];
        }

        return $result;
    }

};
