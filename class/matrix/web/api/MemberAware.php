<?php //>

namespace matrix\web\api;

trait MemberAware {

    private $member;
    private $token;

    public function member() {
        if ($this->member === null) {
            $this->member = false;

            preg_match("/^(Bearer )?(.*)$/", @$_SERVER['HTTP_AUTHORIZATION'], $tokens, PREG_UNMATCHED_AS_NULL);

            $token = model('AuthToken')->find(['token' => $tokens[2], 'type' => 2]);
            $member = !$token || $token['expire_time'] ? null : model('Member')->queryById($token['target_id']);

            if ($member) {
                $member['token'] = $token['token'];

                $this->member = $member;
                $this->token = model('AuthToken')->update($token);
            }
        }

        return $this->member;
    }

    protected function invalidate() {
        if ($this->token && !$this->token['expire_time']) {
            $this->token['expire_time'] = date(cfg('system.timestamp'));

            $this->token = model('AuthToken')->update($this->token);
        }
    }

}
