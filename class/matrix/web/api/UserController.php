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
            define('USER_LEVEL', USER_ID > 1000 ? 3 : (USER_ID > 1 ? 2 : 1));

            return true;
        } else {
            $this->response()->status(401);

            return false;
        }
    }

}
