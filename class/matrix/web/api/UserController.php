<?php //>

namespace matrix\web\api;

use matrix\db\Model;

class UserController extends Controller {

    use UserAware;

    protected function authenticate() {
        $user = $this->user();

        if ($user) {
            Model::enableAdministration();

            define('USER_ID', $user['id']);

            return true;
        } else {
            $this->response()->status(401);

            return false;
        }
    }

}
