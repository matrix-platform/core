<?php //>

namespace matrix\web\api\member;

use matrix\utility\Func;
use matrix\web\api\Controller;
use matrix\web\Security;

class Login extends Controller {

    use Security;

    protected $identity;

    public function __construct($identity = 'username') {
        $this->identity = $identity;
        $this->values = [];
    }

    protected function validate($form) {
        $errors = [];

        foreach ([$this->identity, 'password'] as $name) {
            if (@$form[$name] === null) {
                $errors[] = ['name' => $name, 'type' => 'required'];
            }
        }

        if (!$errors) {
            $type = table('Member')->{$this->identity}->validate($form[$this->identity]);

            if ($type !== true) {
                $errors[] = ['name' => $this->identity, 'type' => $type];
            }
        }

        return $errors;
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

        if ($member['disabled']) {
            return $this->memberDisabled($member);
        }

        if (!Func::verify_password($member, $form['password'])) {
            return [
                'view' => 'login-failed.php',
                'error' => 'error.password-not-matched',
                'member_id' => $member['id'],
            ];
        }

        $model = model('AuthToken');
        $token = sha1(uniqid('', true));

        while ($model->count(['token' => $token])) {
            $token = sha1(uniqid('', true));
        }

        $model->insert([
            'token' => $token,
            'type' => 2,
            'target_id' => $member['id'],
            'user_agent' => @$_SERVER['HTTP_USER_AGENT'],
        ]);

        model('MemberLog')->insert(['member_id' => $member['id'], 'type' => 1]);

        return $this->subprocess($form, ['success' => true, 'id' => $member['id'], 'token' => $token]);
    }

    protected function memberDisabled($member) {
        return ['error' => 'error.member-disabled'];
    }

    protected function queryMember($form) {
        return model('Member')->find([$this->identity => $form[$this->identity]]);
    }

}
