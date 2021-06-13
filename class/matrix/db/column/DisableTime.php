<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class DisableTime {

    use Column, type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'searchStyle' => 'between',
            'validation' => 'timestamp',
        ];

        $this->table()->disableTime($this->name());
    }

}
