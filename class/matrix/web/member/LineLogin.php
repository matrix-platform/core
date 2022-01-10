<?php //>

namespace matrix\web\member;

use matrix\web\Controller;

class LineLogin extends Controller {

    use OAuthLogin;

    public function __construct() {
        $this->values = ['redirect_uri' => 'member/line-login'];
    }

    protected function process($form) {
        $state = $this->get('LINE_LOGIN_STATE');

        if (!$state || $state !== @$form['state']) {
            return ['error' => 'error.invalid-line-state'];
        }

        $line = load_cfg('line');

        //--

        $context = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'grant_type' => 'authorization_code',
                    'code' => @$form['code'],
                    'redirect_uri' => get_url(APP_ROOT . $this->redirect_uri()),
                    'client_id' => $line['client_id'],
                    'client_secret' => $line['client_secret'],
                ]),
            ],
        ];

        $response = file_get_contents($line['token_url'], false, stream_context_create($context));

        if ($response) {
            $response = json_decode($response, true);
        }

        $token = @$response['access_token'];

        if (!$token) {
            return ['error' => 'error.invalid-line-access-token'];
        }

        //--

        $context = [
            'http' => [
                'header' => "Authorization: Bearer {$token}",
            ],
        ];

        $profile = file_get_contents($line['profile_url'], false, stream_context_create($context));

        if ($profile) {
            $profile = json_decode($profile, true);
        }

        $username = @$profile['userId'];

        if (!$username) {
            return ['error' => 'error.invalid-line-profile'];
        }

        //--

        return $this->login([
            'username' => $username,
            'name' => @$profile['displayName'],
        ]);
    }

}
