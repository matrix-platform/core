<?php //>

namespace matrix\web\member\login;

trait Line {

    public function getLineAuthUrl($path = 'member/line-login') {
        $line = load_cfg('line');
        $state = sha1(time());

        $this->set('LINE_LOGIN_STATE', $state);

        return $line['auth_url'] . '?' . http_build_query([
            'client_id' => $line['client_id'],
            'redirect_uri' => url(APP_ROOT . $path),
            'response_type' => 'code',
            'scope' => $line['scope'],
            'state' => $state,
        ]);
    }

}
