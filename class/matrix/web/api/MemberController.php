<?php //>

namespace matrix\web\api;

class MemberController extends Controller {

    use MemberAware;

    protected function authenticate() {
        $member = $this->member();

        if ($member) {
            define('MEMBER_ID', $member['id']);
            define('MEMBER_TOKEN', $member['token']);

            return true;
        } else {
            $this->response()->status(401);

            return false;
        }
    }

}
