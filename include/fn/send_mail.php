<?php //>

use PHPMailer\PHPMailer\PHPMailer;

return new class() {

    public function __invoke($options) {
        if (@$options['queue']) {
            return $this->queue($options);
        } else {
            return $this->gmail($options);
        }
    }

    private function gmail($options) {
        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';
        $mailer->From = $options['username'];
        $mailer->Host = 'smtp.gmail.com';
        $mailer->Password = $options['password'];
        $mailer->Port = 465;
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = 'ssl';
        $mailer->Username = $options['username'];

        $mailer->isHTML(true);
        $mailer->isSMTP();

        $mailer->FromName = $options['from'];
        $mailer->Subject = render($options['subject'], $options);
        $mailer->Body = render($options['content'], $options);

        foreach (preg_split('/[\s;,]/', $options['to'], 0, PREG_SPLIT_NO_EMPTY) as $to) {
            $mailer->AddAddress($to);
        }

        if ($mailer->Send()) {
            model('MailLog')->insert([
                'sender' => $options['username'],
                'receiver' => $options['to'],
                'subject' => $mailer->Subject,
                'content' => $mailer->Body,
            ]);

            return true;
        }

        return false;
    }

    private function queue($options) {
        $log = model('MailLog')->insert([
            'sender' => $options['from'],
            'receiver' => $options['to'],
            'subject' => render($options['subject'], $options),
            'content' => render($options['content'], $options),
        ]);

        if ($log) {
            $cipher = 'aes-128-gcm';
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
            $text = openssl_encrypt(json_encode($log), $cipher, $options['password'], 0, $iv, $tag);

            $request = [
                'http' => [
                    'header' => "Content-Type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode([
                        'mailer' => $options['username'],
                        'data' => $text,
                        'iv' => base64_encode($iv),
                        'tag' => base64_encode($tag),
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
