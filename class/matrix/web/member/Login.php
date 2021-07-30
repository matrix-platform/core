<?php //>

namespace matrix\web\member;

use matrix\web\Controller;

class Login extends Controller {

    use RememberMe;

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $member = $this->queryMember($form);

        if (!$member) {
            return ['error' => 'error.member-not-found'];
        }

        if ($member['password'] !== md5($member['id'] . '::' . @$form['password'])) {
            model('MemberLog')->insert(['member_id' => $member['id'], 'type' => 4]);

            return ['error' => 'error.password-not-matched'];
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

        return ['success' => true];
    }

    protected function queryMember($form) {
        return model('Member')->find(['username' => @$form['username']]);
    }

}
