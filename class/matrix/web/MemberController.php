<?php //>

namespace matrix\web;

class MemberController extends Controller {

    use MemberAware;

    public function execute() {
        if ($this->authorize()) {
            parent::execute();
        }
    }

    protected function authorize() {
        $member = $this->member();

        if ($member) {
            define('MEMBER_ID', $member['id']);

            return true;
        }

        if (defined('AJAX')) {
            header('HTTP/1.1 401 Unauthorized');
        } else {
            $path = base64_urlencode($_SERVER['REQUEST_URI']);

            header('Location: ' . APP_ROOT . 'login/' . $path);
        }

        return false;
    }

}
