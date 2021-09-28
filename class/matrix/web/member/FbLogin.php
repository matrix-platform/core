<?php //>

namespace matrix\web\member;

use Facebook\Facebook;
use matrix\web\Controller;

class FbLogin extends Controller {

    use OAuthLogin;

    protected function process($form) {
        $fb = new Facebook(load_cfg('facebook'));

        $helper = $fb->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();

        if (!$accessToken) {
            return ['error' => 'error.invalid-fb-access-token'];
        }

        $fb->setDefaultAccessToken($accessToken);
        $response = $fb->get('/me?locale=en_US&fields=id,name,email');
        $user = $response->getGraphUser();

        //--

        return $this->login([
            'username' => $user->getField('id'),
            'name' => $user->getField('name'),
            'mail' => $user->getField('email'),
        ]);
    }

}
