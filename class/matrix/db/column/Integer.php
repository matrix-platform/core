<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Integer {

    use Column, type\Integer;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'integer',
            'searchStyle' => 'between',
            'validation' => 'integer',
        ];
    }

}
