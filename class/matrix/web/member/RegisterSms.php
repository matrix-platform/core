<?php //>

namespace matrix\web\member;

use matrix\utility\Func;
use matrix\web\Controller;

class RegisterSms extends Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $register = $this->get('Register');

        if ($register) {
            if ($register['time'] + $register['cooldown'] - time() > 0) {
                return ['error' => 'error.retry-sms-later'];
            }

            if (Func::count_sms(REMOTE_ADDR, $register['cooldown'])) {
                return ['error' => 'error.retry-sms-later'];
            }
        }

        $mobile = @$form['mobile'];

        if ($mobile === null) {
            return ['error' => 'error.mobile-required'];
        }

        if (!$this->validateMobile($mobile)) {
            return ['error' => 'error.invalid-mobile'];
        }

        if (model('Member')->count(['mobile' => $mobile])) {
            return ['error' => 'error.mobile-exists'];
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $options = load_cfg('sms');
        $options['mobile'] = $mobile;
        $options['text'] = render(i18n('sms.register'), ['code' => $code]);

        if (!Func::send_sms($options)) {
            return ['error' => 'error.sms-failed'];
        }

        $this->set('Register', [
            'mobile' => $mobile,
            'code' => $code,
            'cooldown' => cfg('system.sms-cooldown'),
            'time' => time(),
        ]);

        return ['success' => true, 'message' => i18n('common.sms-success')];
    }

    protected function validateMobile($mobile) {
        $pattern = cfg('frontend.mobile-pattern');

        return $pattern ? preg_match($pattern, $mobile) : true;
    }

}
