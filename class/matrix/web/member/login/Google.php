<?php //>

namespace matrix\web\member\login;

use Google\Client;
use Google\Service\Oauth2;

trait Google {

    public function getGoogleAuthUrl($path = 'member/google-login', $config = 'google-oauth2.json') {
        $client = new Client();
        $client->addScope(Oauth2::USERINFO_EMAIL);
        $client->addScope(Oauth2::USERINFO_PROFILE);
        $client->setAuthConfig(APP_DATA . $config);
        $client->setRedirectUri(url(APP_ROOT . $path));

        return $client->createAuthUrl();
    }

}
