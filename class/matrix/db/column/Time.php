<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Time {

    use Column, type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'time',
            'pattern' => cfg('system.time'),
            'searchStyle' => 'between',
            'validation' => 'time',
        ];
    }

}
