<?php //>

// php www/index.php /console/async-send-mail

use PHPMailer\PHPMailer\PHPMailer;

return new class() extends matrix\cli\Controller {

    use matrix\cli\Mutex;

    protected function process($form) {
        $model = model('MailLog');

        foreach ($model->query(['status' => 1], true, 10) as $item) {
            $options = load_cfg($item['sender']);

            if ($this->send($item['receiver'], $item['subject'], $item['content'], $options)) {
                $item['send_time'] = date(cfg('system.timestamp'));
                $item['status'] = 0;
            } else {
                $item['status'] = 9;
            }

            $item['sender'] = $options['username'];

            $model->update($item);

            sleep(2);
        }

        return ['success' => true];
    }

    protected function transaction() {
        return false;
    }

    private function send($receiver, $subject, $body, $options) {
        $mailer = new PHPMailer();

        $mailer->Host = $options['host'];
        $mailer->Port = $options['port'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $options['username'];
        $mailer->Password = $options['password'];

        if ($options['secure']) {
            $mailer->SMTPSecure = $options['secure'];
        } else {
            $mailer->SMTPAutoTLS = false;
        }

        $mailer->isHTML(true);
        $mailer->isSMTP();

        $mailer->CharSet = 'utf-8';
        $mailer->From = $options['username'];
        $mailer->FromName = $options['from'];
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        foreach (preg_split('/[\s;,]/', $receiver, 0, PREG_SPLIT_NO_EMPTY) as $to) {
            $mailer->addBCC($to);
        }

        return $mailer->send();
    }

};
