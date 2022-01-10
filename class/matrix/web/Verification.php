<?php //>

namespace matrix\web;

trait Verification {

    public function verify() {
        if ($this->method() === 'POST') {
            $token = @$_SERVER['HTTP_MATRIX_TOKEN'];

            return $token ? ($token === @$_COOKIE['matrix-token']) : false;
        }

        return true;
    }

}
