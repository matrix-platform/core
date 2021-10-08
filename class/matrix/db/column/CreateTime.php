<?php //>

namespace matrix\db\column;

class CreateTime {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'formStyle' => 'timestamp',
            'pattern' => cfg('system.timestamp'),
            'readonly' => true,
            'searchStyle' => 'between',
        ];
    }

    public function generate($value) {
        return date($this->pattern());
    }

}
