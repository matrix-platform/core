<?php //>

namespace matrix\db\column\type;

use PDO;

trait File {

    public function convert($value) {
        return strval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
