<?php //>

namespace matrix\db\column\type;

use PDO;

trait Double {

    public function convert($value) {
        return doubleval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
