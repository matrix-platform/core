<?php //>

namespace matrix\db\column;

class Time extends AbstractDateTime {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'time',
            'pattern' => cfg('system.time'),
            'validation' => 'time',
        ]);
    }

}
