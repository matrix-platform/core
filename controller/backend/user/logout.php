<?php //>

return new class() extends matrix\web\UserController {

    protected function process($form) {
        $this->remove('User');

        model('UserLog')->insert(['user_id' => USER_ID, 'type' => 2]);

        return ['success' => true, 'type' => 'reload'];
    }

};
