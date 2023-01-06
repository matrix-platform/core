<?php //>

namespace matrix\web\member;

use Google\Client;
use Google\Service\Oauth2;
use matrix\web\Controller;

class GoogleLogin extends Controller {

    use OAuthLogin;

    public function __construct() {
        $this->values = ['config' => 'google-oauth2.json'];
    }

    protected function process($form) {
        $client = new Client();
        $client->setAuthConfig(APP_DATA . $this->config());
        $client->setRedirectUri(get_url(APP_ROOT . substr($this->path(), 1)));

        $auth = $client->authenticate(@$form['code']);

        if (empty($auth['access_token'])) {
            return ['error' => 'error.invalid-google-access-token'];
        }

        $service = new Oauth2($client);
        $info = $service->userinfo->get();

        //--

        return $this->login([
            'username' => $info->id,
            'name' => $info->name,
            'mail' => $info->email,
        ]);
    }

}
