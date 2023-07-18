<?php //>

use matrix\utility\Func;

return new class() {

    public function __invoke($args) {
        $args['receiver'] = $args['mobile'];

        if ($args['prefix']) {
            $args['mobile'] = $args['prefix'] . ltrim($args['mobile'], '0');
        }

        return $this->{$args['method']}($args);
    }

    private function log($args, $response) {
        model('SmsLog')->insert([
            'receiver' => $args['receiver'],
            'content' => $args['text'],
            'response' => $response,
        ]);
    }

    private function notify($current, $safe) {
        $content = load_i18n('template/sms-warning');

        if ($content['to']) {
            $content['async'] = true;
            $content['current'] = $current;
            $content['safe'] = $safe;

            Func::send_mail(array_merge(load_cfg($content['mailer']), $content));
        }
    }

    private function as1010($args) {
        if ($args['key'] === '00000') {
            $response = 1;
        } else {
            $response = intval(file_get_contents(render($args['url'], $args)));
        }

        if ($response === 1) {
            $this->log($args, $response);

            return true;
        }

        if ($response === 4) {
            $this->notify(0, $args['safe-point']);
        }

        return false;
    }

    private function every8d($args) {
        $context = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
                'content' => http_build_query([
                    'UID' => $args['key'],
                    'PWD' => $args['screct'],
                    'SB' => '',
                    'MSG' => $args['text'],
                    'DEST' => $args['mobile'],
                    'ST' => '',
                ]),
            ],
        ];

        $response = file_get_contents($args['url'], false, stream_context_create($context));
        $credit = intval(strtok($response, ','));

        if ($credit > 0) {
            $this->log($args, $response);

            if ($credit < $args['safe-point']) {
                $this->notify($credit, $args['safe-point']);
            }

            return true;
        }

        return false;
    }

    private function mitake($args) {
        if ($args['key'] === '00000') {
            $response = 'statuscode=1';
        } else {
            $response = file_get_contents(render($args['url'], $args));
        }

        if (preg_match('/statuscode=(\d+)/', $response, $matches) && $matches[1] < 5) {
            $this->log($args, $response);

            if (preg_match('/AccountPoint=(\d+)/', $response, $matches) && $matches[1] < $args['safe-point']) {
                $this->notify($matches[1], $args['safe-point']);
            }

            return true;
        }

        return false;
    }

    private function sms1111($args) {
        if ($args['key'] === '00000') {
            $response = '{"STATUS":"1_OK"}';
        } else {
            $response = file_get_contents(render($args['url'], $args));
        }

        $result = json_decode($response, true);

        if ($result) {
            $this->log($args, $response);

            $status = @$result['STATUS'];

            if ($status) {
                list($status, $message) = preg_split('/_/', $status, 2);

                if ($status === '1') {
                    return true;
                }

                return $message;
            }
        }

        return false;
    }

    private function smsget($args) {
        if ($args['key'] === '00000') {
            $response = '{"stats":true,"error_code":"000","error_msg":"0|1|99999"}';
        } else {
            $response = file_get_contents(render($args['url'], $args));
        }

        $result = json_decode($response, true);

        if ($result && $result['stats']) {
            $this->log($args, $response);

            $tokens = preg_split('/\|/', $result['error_msg']);

            if ($tokens[2] < $args['safe-point']) {
                $this->notify($matches[1], $args['safe-point']);
            }

            return true;
        }

        return false;
    }

};
