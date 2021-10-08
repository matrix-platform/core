<?php //>

namespace matrix\db\column;

class Time {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'time',
            'pattern' => cfg('system.time'),
            'searchStyle' => 'between',
            'validation' => 'time',
        ];
    }

}
