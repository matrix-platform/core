<?php //>

namespace matrix\db\column;

use matrix\db\Column;
use PDO;

abstract class AbstractDateTime extends Column {

    public function __construct($values) {
        parent::__construct($values + ['searchStyle' => 'between']);
    }

    public function convert($value) {
        return is_int($value) ? date($this->pattern(), $value) : $value;
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
