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

        $log = [
            'mailer' => $options['mailer'],
            'sender' => $options['username'],
            'receiver' => $options['to'],
            'subject' => render($options['subject'], $options),
            'content' => render($options['content'], $options),
        ];

        if (@$options['async']) {
            $log['status'] = 1;

            $result = true;
        } else {
            $mailer = Func::create_mailer($log['receiver'], $log['subject'], $log['content'], $options);

            $result = $mailer->send();

            if (!$result) {
                logging('error')->error($mailer->ErrorInfo);

                $log['status'] = 9;
            }
        }

        model('MailLog')->insert($log);

        return $result;
    }

    private function queue($options) {
        $log = model('MailLog')->insert([
            'mailer' => $options['queue'],
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
