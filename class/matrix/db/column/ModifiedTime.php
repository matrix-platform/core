<?php //>

namespace matrix\db\column;

class ModifiedTime extends Timestamp {

    public function __construct($values) {
        parent::__construct($values + [
            'blankStyle' => 'hidden',
            'disabled' => true,
        ]);
    }

    public function generate($value) {
        return date($this->pattern());
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
