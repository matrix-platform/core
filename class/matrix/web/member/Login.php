<?php //>

namespace matrix\web\member;

use matrix\web\Controller;
use matrix\web\Security;

class Login extends Controller {

    use RememberMe, Security;

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        if ($this->isLocked(table('MemberLog'), ['ip' => REMOTE_ADDR], 'ip')) {
            return ['error' => 'error.login-failed'];
        }

        $member = $this->queryMember($form);

        if (!$member) {
            return ['error' => 'error.member-not-found'];
        }

        if ($this->isLocked(table('MemberLog'), ['member_id' => $member['id']], 'member')) {
            return ['error' => 'error.login-failed'];
        }

        if ($member['password'] !== md5($member['id'] . '::' . @$form['password'])) {
            return [
                'view' => 'login-failed.php',
                'error' => 'error.password-not-matched',
                'member_id' => $member['id'],
            ];
        }

        if ($member['disabled']) {
            return ['error' => 'error.member-disabled'];
        }

        $this->set('Member', $member);

        model('MemberLog')->insert(['member_id' => $member['id'], 'type' => 1]);

        if (@$form['remember']) {
            $this->remember($member);
        } else {
            $this->forget();
        }

        return $this->subprocess($form, ['success' => true]);
    }

    protected function queryMember($form) {
        return model('Member')->find(['username' => @$form['username']]);
    }

}
