<?php //>

use PHPMailer\PHPMailer\PHPMailer;
use matrix\utility\Func;

return new class() {

    public function __invoke($options) {
        if (@$options['queue']) {
            return $this->queue($options);
        } else {
            return $this->send($options);
        }
    }

    private function send($options) {
        if (@$options['sandbox']) {
            $options['subject'] = "{$options['subject']} ({$options['to']})";
            $options['to'] = $options['sandbox'];
        }

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
        $mailer->Subject = render($options['subject'], $options);
        $mailer->Body = render($options['content'], $options);

        foreach (preg_split('/[\s;,]/', $options['to'], 0, PREG_SPLIT_NO_EMPTY) as $to) {
            $mailer->addBCC($to);
        }

        $log = [
            'sender' => $options['username'],
            'receiver' => $options['to'],
            'subject' => $mailer->Subject,
            'content' => $mailer->Body,
        ];

        $result = $mailer->send();

        if (!$result) {
            logging('error')->error($mailer->ErrorInfo);

            $log['status'] = 9;
        }

        model('MailLog')->insert($log);

        return $result;
    }

    private function queue($options) {
        $log = model('MailLog')->insert([
            'sender' => $options['from'],
            'receiver' => $options['to'],
            'subject' => render($options['subject'], $options),
            'content' => render($options['content'], $options),
        ]);

        if ($log) {
            $request = [
                'http' => [
                    'header' => "Content-Type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode([
                        'mailer' => $options['username'],
                        'data' => Func::encrypt(json_encode($log), $options['password']),
                    ]),
                ],
            ];

            $response = @file_get_contents($options['queue'], false, stream_context_create($request));
            $result = json_decode($response, true);

            if ($result && @$result['success']) {
                return true;
            }
        }

        return false;
    }

};
