<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use PDO;

trait Integer {

    use Column;

    public function convert($value) {
        return intval($value);
    }

    public function type() {
        return PDO::PARAM_INT;
    }

}
