<?php //>

namespace matrix\db\column;

use matrix\db\Column;
use PDO;

class Double extends Column {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'double',
            'searchStyle' => 'between',
            'validation' => 'double',
        ]);
    }

    public function convert($value) {
        return doubleval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
