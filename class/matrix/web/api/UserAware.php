<?php //>

namespace matrix\web\api;

trait UserAware {

    private $user;

    public function user() {
        if ($this->user === null) {
            $this->user = false;

            preg_match("/^(Bearer )?(.*)$/", @$_SERVER['HTTP_AUTHORIZATION'], $tokens, PREG_UNMATCHED_AS_NULL);

            $token = model('AuthToken')->find(['token' => $tokens[2], 'type' => 1]);
            $user = !$token || $token['expire_time'] ? null : model('User')->queryById($token['target_id']);

            if ($user) {
                $this->user = $user;
            }
        }

        return $this->user;
    }

}
