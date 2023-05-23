<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use PDO;

trait Integer {

    use Column;

    public function convert($value) {
        $value = intval($value);

        return $value > 2147483647 || $value < -2147483648 ? 0 : $value;
    }

    public function type() {
        return PDO::PARAM_INT;
    }

}
