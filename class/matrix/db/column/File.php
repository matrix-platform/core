<?php //>

namespace matrix\db\column;

use matrix\db\Column;
use PDO;

class File extends Column {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'file',
            'unordered' => true,
        ]);
    }

    public function convert($value) {
        return strval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
