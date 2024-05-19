<?php //>

use matrix\utility\Func;

return new class() extends matrix\web\Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $user = model('User')->queryByUsername(@$form['username']);

        if (!$user) {
            return ['error' => 'error.user-not-found'];
        }

        if (table('UserLog')->filter(['user_id' => $user['id'], 'type' => 1, 'timestamp' => @$form['timestamp']])->count()) {
            return ['error' => 'error.login-failed'];
        }

        if (!Func::verify_password($user, @$form['password'])) {
            model('UserLog')->insert(['user_id' => $user['id'], 'type' => 4]);

            return ['success' => true, 'view' => 'error.php', 'error' => 'error.password-not-matched'];
        }

        $this->set('User', $user);

        model('UserLog')->insert(['user_id' => $user['id'], 'type' => 1, 'timestamp' => $form['timestamp']]);

        return ['success' => true];
    }

};
