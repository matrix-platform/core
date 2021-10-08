<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use PDO;

trait Boolean {

    use Column;

    public function convert($value) {
        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return boolval($value);
    }

    public function type() {
        return PDO::PARAM_BOOL;
    }

}
