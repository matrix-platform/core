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
            $content['current'] = $current;
            $content['safe'] = $safe;

            Func::send_mail(array_merge(load_cfg($content['mailer']), $content));
        }
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

};
