<?php //>

namespace matrix\web\member;

use Facebook\Facebook;
use matrix\web\Controller;
use matrix\web\MemberAware;

class LoginForm extends Controller {

    use MemberAware;

    public function __construct() {
        $this->values = ['view' => cfg('frontend.login-form')];
    }

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/[\w-]+)?$/", $this->path());
        }

        return false;
    }

    public function getFbAuthUrl($path = 'member/fb-login') {
        $fb = new Facebook(load_cfg('facebook'));
        $url = url(APP_ROOT . $path);

        return $fb->getRedirectLoginHelper()->getLoginUrl($url, ['email']);
    }

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

    protected function process($form) {
        $args = $this->args();
        $path = $args ? base64_urldecode($args[0]) : APP_ROOT;

        $result = ['success' => true, 'path' => $path];

        if ($this->member()) {
            $result['view'] = '302.php';
        }

        return $result;
    }

}
