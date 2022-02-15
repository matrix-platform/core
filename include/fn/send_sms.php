<?php //>

use matrix\utility\Func;

return new class() {

    public function __invoke($args) {
        return $this->{$args['method']}($args);
    }

    private function log($args, $response) {
        model('SmsLog')->insert([
            'receiver' => $args['receiver'],
            'content' => $args['text'],
            'response' => $response,
        ]);
    }

    private function mitake($args) {
        $args['receiver'] = $args['mobile'];

        if ($args['prefix']) {
            $args['mobile'] = $args['prefix'] . ltrim($args['mobile'], '0');
        }

        if ($args['key'] === '00000') {
            $response = 'statuscode=1';
        } else {
            $response = file_get_contents(render($args['url'], $args));
        }

        if (preg_match('/statuscode=(\d+)/', $response, $matches) && $matches[1] < 5) {
            $this->log($args, $response);

            if (preg_match('/AccountPoint=(\d+)/', $response, $matches) && $matches[1] < $args['safe-point']) {
                $content = load_i18n('template/mitake-warning');

                if ($content['to']) {
                    $content['current'] = $matches[1];
                    $content['safe'] = $args['safe-point'];

                    Func::send_mail(array_merge(load_cfg($content['mailer']), $content));
                }
            }

            return true;
        }

        return false;
    }

};
