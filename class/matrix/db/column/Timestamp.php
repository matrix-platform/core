<?php //>

namespace matrix\db\column;

class Timestamp extends AbstractDateTime {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'validation' => 'timestamp',
        ]);
    }

}
