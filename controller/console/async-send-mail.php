<?php //>

// php www/index.php /console/async-send-mail

use matrix\utility\Func;

return new class() extends matrix\cli\Controller {

    use matrix\cli\Mutex;

    protected function process($form) {
        $model = model('MailLog');

        foreach ($model->query(['status' => 1], true, 10) as $item) {
            $options = load_cfg($item['mailer']);

            if ($this->send($item['receiver'], $item['subject'], $item['content'], $options)) {
                $item['send_time'] = date(cfg('system.timestamp'));
                $item['status'] = 0;
            } else {
                $item['status'] = 9;
            }

            $model->update($item);

            sleep(2);
        }

        return ['success' => true];
    }

    protected function transaction() {
        return false;
    }

    private function send($receiver, $subject, $body, $options) {
        $mailer = Func::create_mailer($receiver, $subject, $body, $options);

        return $mailer->send();
    }

};
