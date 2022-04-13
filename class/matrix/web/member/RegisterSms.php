<?php //>

namespace matrix\web\member;

use matrix\utility\Func;
use matrix\web\Controller;

class RegisterSms extends Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function mobileExists($mobile) {
        return model('Member')->count(['mobile' => $mobile]);
    }

    protected function process($form) {
        $register = $this->get('Register');

        if ($register) {
            if ($register['time'] + $register['cooldown'] - time() > 0) {
                return ['error' => 'error.retry-sms-later'];
            }
        }

        $cooldown = cfg('system.sms-cooldown');

        if (Func::count_sms(REMOTE_ADDR, $cooldown)) {
            return ['error' => 'error.retry-sms-later'];
        }

        $mobile = @$form['mobile'];

        if ($mobile === null) {
            return ['error' => 'error.mobile-required'];
        }

        if (!$this->validateMobile($mobile)) {
            return ['error' => 'error.invalid-mobile'];
        }

        if ($this->mobileExists($mobile)) {
            return ['error' => 'error.mobile-exists'];
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        if (!$this->sendSms($mobile, $code)) {
            return ['error' => 'error.sms-failed'];
        }

        $this->set('Register', [
            'mobile' => $mobile,
            'code' => $code,
            'cooldown' => $cooldown,
            'data' => $form,
            'time' => time(),
        ]);

        return ['success' => true, 'message' => i18n('common.sms-success')];
    }

    protected function sendSms($mobile, $code) {
        $options = load_cfg('sms');
        $options['mobile'] = $mobile;
        $options['text'] = render(i18n('sms.register'), ['code' => $code]);

        return Func::send_sms($options);
    }

    protected function validateMobile($mobile) {
        $pattern = cfg('frontend.mobile-pattern');

        return $pattern ? preg_match($pattern, $mobile) : true;
    }

}
