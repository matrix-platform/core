<?php //>

namespace matrix\web\member;

use matrix\utility\Func;
use matrix\web\Controller;

class ForgotSms extends Controller {

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $forgot = $this->get('Forgot');

        if ($forgot) {
            if ($forgot['time'] + $forgot['cooldown'] - time() > 0) {
                return ['error' => 'error.retry-sms-later'];
            }
        }

        $cooldown = cfg('system.sms-cooldown');

        if (Func::count_sms(REMOTE_ADDR, $cooldown)) {
            return ['error' => 'error.retry-sms-later'];
        }

        $member = $this->queryMember($form);

        if (!$member) {
            return ['error' => 'error.member-not-found'];
        }

        if ($member['disabled']) {
            return ['error' => 'error.member-disabled'];
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        if (!$this->sendSms($member['mobile'], $code)) {
            return ['error' => 'error.sms-failed'];
        }

        $this->set('Forgot', [
            'member_id' => $member['id'],
            'code' => $code,
            'cooldown' => $cooldown,
            'time' => time(),
        ]);

        return ['success' => true, 'message' => i18n('common.sms-success')];
    }

    protected function queryMember($form) {
        return model('Member')->find(['mobile' => @$form['mobile']]);
    }

    protected function sendSms($mobile, $code) {
        $options = load_cfg('sms');
        $options['mobile'] = $mobile;
        $options['text'] = render(i18n('sms.forgot'), ['code' => $code]);

        return Func::send_sms($options);
    }

}
