<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class CreateTime {

    use Column, type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'readonly' => true,
            'searchStyle' => 'between',
        ];
    }

    public function generate($value) {
        return date($this->pattern());
    }

}
