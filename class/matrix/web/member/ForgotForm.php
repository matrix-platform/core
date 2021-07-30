<?php //>

namespace matrix\web\member;

use matrix\web\Controller;

class ForgotForm extends Controller {

    public function __construct() {
        $this->values = ['view' => cfg('frontend.forgot-form')];
    }

    protected function process($form) {
        $forgot = $this->get('Forgot');

        if ($forgot) {
            $time = $forgot['time'] + $forgot['cooldown'] - time();

            if ($time < 0) {
                $time = 0;
            }
        } else {
            $time = 0;
        }

        return [
            'success' => true,
            'member' => $time ? model('Member')->get($forgot['member_id']) : null,
            'time' => $time,
        ];
    }

}
