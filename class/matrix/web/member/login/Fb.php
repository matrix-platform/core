<?php //>

namespace matrix\web\member\login;

use Facebook\Facebook;

trait Fb {

    public function getFbAuthUrl($path = 'member/fb-login') {
        $fb = new Facebook(load_cfg('facebook'));
        $url = get_url(APP_ROOT . $path);

        return $fb->getRedirectLoginHelper()->getLoginUrl($url, ['email']);
    }

}
