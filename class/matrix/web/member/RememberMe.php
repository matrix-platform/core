<?php //>

namespace matrix\web\member;

use matrix\web\RememberMe as AbstractRememberMe;

trait RememberMe {

    use AbstractRememberMe;

    protected function getTokenName() {
        return 'matrix-m';
    }

}
