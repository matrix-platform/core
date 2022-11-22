<?php //>

use matrix\utility\Func;

return new class() extends matrix\web\UserController {

    protected function init() {
        $this->view(cfg('backend.password-changed'));
    }

    protected function validate($form) {
        $errors = [];
        $user = $this->user();

        $current = @$form['current'];

        if ($current === null) {
            $errors[] = ['name' => 'current', 'type' => 'required'];
        } else if (!Func::verify_password($user, $current)) {
            $errors[] = ['name' => 'current', 'message' => i18n('error.password-not-matched')];
        }

        $password = @$form['password'];

        if ($password === null) {
            $errors[] = ['name' => 'password', 'type' => 'required'];
        }

        $confirm = @$form['confirm'];

        if ($confirm === null) {
            $errors[] = ['name' => 'confirm', 'type' => 'required'];
        } else if ($confirm !== $password) {
            $errors[] = ['name' => 'confirm', 'message' => i18n('error.inconsistent-passwords')];
        }

        return $errors;
    }

    protected function process($form) {
        $user = $this->user();
        $user['password'] = $form['password'];

        $user = model('User')->update($user);

        if ($user === null) {
            return ['error' => 'error.user-not-found'];
        }

        if ($user === false) {
            return ['error' => 'error.password-failed'];
        }

        $this->set('User', $user);

        model('UserLog')->insert(['user_id' => $user['id'], 'type' => 3]);

        return ['success' => true];
    }

};
