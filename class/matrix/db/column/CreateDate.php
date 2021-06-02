<?php //>

namespace matrix\db\column;

class CreateDate extends Date {

    public function __construct($values) {
        parent::__construct($values + [
            'blankStyle' => 'hidden',
            'readonly' => true,
            'required' => true,
        ]);
    }

    public function generate($value) {
        return date($this->pattern());
    }

}
