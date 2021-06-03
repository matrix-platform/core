<?php //>

namespace matrix\web;

class UserController extends Controller {

    use UserAware;

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    public function execute() {
        if ($this->authorize()) {
            parent::execute();
        }
    }

    protected function authorize() {
        $user = $this->user();

        if ($user) {
            define('USER_ID', $user['id']);

            return true;
        }

        if (defined('AJAX')) {
            header('HTTP/1.1 401 Unauthorized');
        } else {
            $path = base64_urlencode($_SERVER['REQUEST_URI']);

            header('Location: ' . APP_ROOT . 'backend/login/' . $path);
        }

        return false;
    }

}
