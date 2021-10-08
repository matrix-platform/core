<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use PDO;

trait Double {

    use Column;

    public function convert($value) {
        return doubleval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
