<?php //>

namespace matrix\db\column;

class Timestamp {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'searchStyle' => 'between',
            'validation' => 'timestamp',
        ];
    }

}
