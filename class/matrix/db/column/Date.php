<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Date {

    use Column, type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'date',
            'pattern' => cfg('system.date'),
            'searchStyle' => 'between',
            'validation' => 'date',
        ];
    }

}
