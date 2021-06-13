<?php //>

namespace matrix\db\column\type;

use PDO;

trait Integer {

    public function convert($value) {
        return intval($value);
    }

    public function type() {
        return PDO::PARAM_INT;
    }

}
