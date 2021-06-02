<?php //>

namespace matrix\db\column;

class Date extends AbstractDateTime {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'date',
            'pattern' => cfg('system.date'),
            'validation' => 'date',
        ]);
    }

}
