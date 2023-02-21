<?php //>

use matrix\utility\Func;

return new class() extends matrix\web\backend\Controller {

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $model = model('MailLog');
        $data = $model->get($this->args()[0]);

        if (!$data) {
            return ['error' => 'error.data-not-found'];
        }

        $mailer = Func::create_mailer($data['receiver'], $data['subject'], $data['content'], load_cfg($data['mailer']));

        if (!$mailer->send()) {
            return ['error' => 'error.resend-failed'];
        }

        $data['send_time'] = date(cfg('system.timestamp'));
        $data['status'] = 0;

        $model->update($data);

        return [
            'success' => true,
            'type' => 'refresh',
            'message' => i18n('backend.resend-success'),
        ];
    }

};
