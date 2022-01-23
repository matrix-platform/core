<?php //>

namespace matrix\web\api;

class VendorController extends Controller {

    use VendorAware;

    protected function authenticate() {
        $vendor = $this->vendor();

        if ($vendor) {
            define('VENDOR_ID', $vendor['id']);

            return true;
        } else {
            $this->response()->status(401);

            return false;
        }
    }

}
