<?php //>

namespace matrix\web\api;

trait BearerAware {

    private $bearer;

    public function bearer() {
        if ($this->bearer === null) {
            preg_match("/^(Bearer )?(.*)$/", @$_SERVER['HTTP_AUTHORIZATION'], $tokens, PREG_UNMATCHED_AS_NULL);

            $this->bearer = $tokens[2];
        }

        return $this->bearer;
    }

}
