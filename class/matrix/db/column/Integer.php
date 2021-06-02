<?php //>

namespace matrix\db\column;

use matrix\db\Column;
use PDO;

class Integer extends Column {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'integer',
            'searchStyle' => 'between',
            'validation' => 'integer',
        ]);
    }

    public function convert($value) {
        return intval($value);
    }

    public function type() {
        return PDO::PARAM_INT;
    }

}
