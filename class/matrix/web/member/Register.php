<?php //>

namespace matrix\web\member;

use matrix\core\AppException;
use matrix\web\Controller;

class Register extends Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $register = $this->get('Register');

        if (!$register || $register['code'] !== @$form['code']) {
            return ['error' => 'error.verification-code-not-matched'];
        }

        if (time() - $register['time'] > cfg('system.verification-code-timeout')) {
            return ['error' => 'error.verification-code-timeout'];
        }

        $password = @$form['password'];

        if (!$this->validatePassword($password)) {
            return ['error' => 'error.invalid-password'];
        }

        if ($password !== @$form['confirm']) {
            return ['error' => 'error.password-not-confirmed'];
        }

        //--

        $member = $this->saveMember($register, $form);

        if ($member) {
            $this->data($member);
        } else {
            return ['error' => 'error.insert-failed'];
        }

        $this->remove('Register');

        return ['success' => true, 'message' => i18n('common.register-success')];
    }

    protected function saveMember($register, $form) {
        if (@$form['agree'] !== 'agree') {
            throw new AppException('error.agreement-required');
        }

        $model = model('Member');
        $mobile = $register['mobile'];

        if ($model->count(['mobile' => $mobile])) {
            throw new AppException('error.mobile-exists');
        }

        return $model->insert([
            'username' => $mobile,
            'mobile' => $mobile,
            'password' => $form['password'],
        ]);
    }

    protected function validatePassword($password) {
        $pattern = cfg('frontend.password-pattern');

        return $pattern ? preg_match($pattern, $password) : true;
    }

}
