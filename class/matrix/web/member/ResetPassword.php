<?php //>

namespace matrix\web\member;

use matrix\web\Controller;

class ResetPassword extends Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $forgot = $this->get('Forgot');

        if (!$forgot || $forgot['code'] !== @$form['code']) {
            return ['error' => 'error.verification-code-not-matched'];
        }

        if (time() - $forgot['time'] > cfg('system.verification-code-timeout')) {
            return ['error' => 'error.verification-code-timeout'];
        }

        $member = model('Member')->get($forgot['member_id']);

        if (!$member) {
            return ['error' => 'error.member-not-found'];
        }

        if ($member['disabled']) {
            return ['error' => 'error.member-disabled'];
        }

        $password = @$form['password'];

        if (!$this->validatePassword($password)) {
            return ['error' => 'error.invalid-password'];
        }

        if ($password !== @$form['confirm']) {
            return ['error' => 'error.password-not-confirmed'];
        }

        $member['password'] = $password;

        if (!model('Member')->update($member)) {
            return ['error' => 'error.update-failed'];
        }

        $this->remove('Forgot');

        model('MemberLog')->insert(['member_id' => $member['id'], 'type' => 5]);

        return ['success' => true, 'message' => i18n('common.reset-password-success')];
    }

    protected function validatePassword($password) {
        $pattern = cfg('frontend.password-pattern');

        return preg_match("/^{$pattern}$/", $password);
    }

}
