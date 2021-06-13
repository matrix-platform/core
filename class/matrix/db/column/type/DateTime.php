<?php //>

namespace matrix\db\column\type;

use PDO;

trait DateTime {

    public function convert($value) {
        return is_int($value) ? date($this->pattern(), $value) : $value;
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
