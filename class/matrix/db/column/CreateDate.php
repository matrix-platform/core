<?php //>

namespace matrix\db\column;

class CreateDate {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'formStyle' => 'date',
            'pattern' => cfg('system.date'),
            'readonly' => true,
            'searchStyle' => 'between',
        ];
    }

    public function generate($value) {
        return date($this->pattern());
    }

}
