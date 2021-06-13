<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class ModifiedTime {

    use Column, type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'disabled' => true,
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'searchStyle' => 'between',
        ];
    }

    public function generate($value) {
        return date($this->pattern());
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
