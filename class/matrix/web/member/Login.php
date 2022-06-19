<?php //>

namespace matrix\web\member;

use matrix\web\Controller;

class Login extends Controller {

    use RememberMe;

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        if ($this->isLocked(['ip' => REMOTE_ADDR], cfg('security.ip-seconds'), cfg('security.ip-count'))) {
            return ['error' => 'error.login-failed'];
        }

        $member = $this->queryMember($form);

        if (!$member) {
            return ['error' => 'error.member-not-found'];
        }

        if ($this->isLocked(['member_id' => $member['id']], cfg('security.member-seconds'), cfg('security.member-count'))) {
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

    private function isLocked($conditions, $seconds, $count) {
        if ($seconds <= 0 || $count <= 0) {
            return false;
        }

        $conditions['type'] = 4;

        $table = table('MemberLog');

        $timestamp = date(cfg('system.timestamp'), time() - $seconds);
        $conditions[] = $table->create_time->GreaterThan($timestamp);

        return $table->model()->count($conditions) >= $count;
    }

}
