<?php //>

namespace matrix\web\member;

use matrix\utility\Fn;
use matrix\web\Controller;

class ForgotSms extends Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $forgot = $this->get('Forgot');

        if ($forgot && $forgot['time'] + $forgot['cooldown'] - time() > 0) {
            return ['error' => 'error.retry-sms-later'];
        }

        if (Fn::count_sms(REMOTE_ADDR, $forgot['cooldown'])) {
            return ['error' => 'error.retry-sms-later'];
        }

        $member = model('Member')->find(['mobile' => @$form['mobile']]);

        if (!$member) {
            return ['error' => 'error.mobile-not-found'];
        }

        if ($member['disabled']) {
            return ['error' => 'error.member-disabled'];
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $options = load_cfg('sms');
        $options['mobile'] = $member['mobile'];
        $options['text'] = render(i18n('sms.forgot'), ['code' => $code]);

        if (!Fn::send_sms($options)) {
            return ['error' => 'error.sms-failed'];
        }

        $this->set('Forgot', [
            'member_id' => $member['id'],
            'code' => $code,
            'cooldown' => cfg('system.sms-cooldown'),
            'time' => time(),
        ]);

        return ['success' => true, 'message' => i18n('common.sms-success')];
    }

}
