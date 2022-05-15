<?php //>

namespace matrix\web;

class MemberController {

    use MemberAware, RequestHandler, Session;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function available() {
        return ($this->method() === 'GET' && $this->name() === $this->path());
    }

    public function execute() {
        if ($this->authenticate()) {
            $this->handle();
        }
    }

    protected function authenticate() {
        $member = $this->member();

        if ($member) {
            define('MEMBER_ID', $member['id']);

            return true;
        }

        if (defined('AJAX')) {
            $this->response()->status(401);
        } else {
            $this->redirect();
        }

        return false;
    }

    protected function redirect() {
        $this->response()->redirect(APP_ROOT . cfg('frontend.login-path') . '/' . base64_urlencode($_SERVER['REQUEST_URI']));
    }

}
