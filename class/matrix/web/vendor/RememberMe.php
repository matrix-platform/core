<?php //>

namespace matrix\web\vendor;

use matrix\web\RememberMe as AbstractRememberMe;

trait RememberMe {

    use AbstractRememberMe;

    protected function getTokenName() {
        return 'matrix-v';
    }

}
