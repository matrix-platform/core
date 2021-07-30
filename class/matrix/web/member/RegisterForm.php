<?php //>

namespace matrix\web\member;

use matrix\web\Controller;

class RegisterForm extends Controller {

    public function __construct() {
        $this->values = ['view' => cfg('frontend.register-form')];
    }

    protected function process($form) {
        $register = $this->get('Register');

        if ($register) {
            $time = $register['time'] + $register['cooldown'] - time();

            if ($time < 0) {
                $time = 0;
            }
        } else {
            $time = 0;
        }

        return [
            'success' => true,
            'register' => $time ? $register : null,
            'time' => $time,
        ];
    }

}
