<?php //>

namespace matrix\db\column;

class CreateTime extends Timestamp {

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
