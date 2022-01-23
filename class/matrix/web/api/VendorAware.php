<?php //>

namespace matrix\web\api;

trait VendorAware {

    private $vendor;

    public function vendor() {
        if ($this->vendor === null) {
            $this->vendor = false;

            preg_match("/^(Bearer )?(.*)$/", @$_SERVER['HTTP_AUTHORIZATION'], $tokens, PREG_UNMATCHED_AS_NULL);

            $token = model('AuthToken')->find(['token' => $tokens[2], 'type' => 3]);
            $vendor = !$token || $token['expire_time'] ? null : model('Vendor')->queryById($token['target_id']);

            if ($vendor) {
                $this->vendor = $vendor;
            }
        }

        return $this->vendor;
    }

}
