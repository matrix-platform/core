<?php //>

namespace matrix\web\api;

trait MemberAware {

    private $member;

    public function member() {
        if ($this->member === null) {
            $this->member = false;

            preg_match("/^(Bearer )?(.*)$/", @$_SERVER['HTTP_AUTHORIZATION'], $tokens, PREG_UNMATCHED_AS_NULL);

            $token = model('AuthToken')->find(['token' => $tokens[2], 'type' => 2]);
            $member = !$token || $token['expire_time'] ? null : model('Member')->queryById($token['target_id']);

            if ($member) {
                $this->member = $member;
            }
        }

        return $this->member;
    }

}
