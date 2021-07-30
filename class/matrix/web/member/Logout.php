<?php //>

namespace matrix\web\member;

use matrix\web\Controller;
use matrix\web\MemberAware;

class Logout extends Controller {

    use MemberAware, RememberMe;

    public function available() {
        return ($this->method() === 'POST' && $this->name() === $this->path());
    }

    protected function process($form) {
        $member = $this->member();

        if ($member) {
            model('MemberLog')->insert(['member_id' => $member['id'], 'type' => 2]);

            $this->forget();
            $this->remove('Member');
        }

        return ['success' => true, 'type' => 'reload'];
    }

}
