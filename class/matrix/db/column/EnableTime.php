<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class EnableTime {

    use Column, type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'timestamp',
            'group' => 'arrange',
            'pattern' => cfg('system.timestamp'),
            'searchStyle' => 'between',
            'tab' => 'other',
            'validation' => 'timestamp',
        ];

        $this->table()->enableTime($this->name());
    }

}
