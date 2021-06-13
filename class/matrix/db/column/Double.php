<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Double {

    use Column, type\Double;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'double',
            'searchStyle' => 'between',
            'validation' => 'double',
        ];
    }

}
