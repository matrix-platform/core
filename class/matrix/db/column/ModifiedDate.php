<?php //>

namespace matrix\db\column;

class ModifiedDate {

    use type\DateTime;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'disabled' => true,
            'formStyle' => 'date',
            'pattern' => cfg('system.date'),
            'searchStyle' => 'between',
        ];
    }

    public function generate($value) {
        return date($this->pattern());
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
