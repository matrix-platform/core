<?php //>

namespace matrix\db\column;

class DisableTime {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'searchStyle' => 'between',
            'tab' => 'other',
            'validation' => 'timestamp',
        ];

        $this->table()->disableTime($this->name());
    }

}
