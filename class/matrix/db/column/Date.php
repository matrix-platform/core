<?php //>

namespace matrix\db\column;

class Date {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'date',
            'pattern' => cfg('system.date'),
            'searchStyle' => 'between',
            'validation' => 'date',
        ];
    }

}
