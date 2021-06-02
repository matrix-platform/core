<?php //>

namespace matrix\db\column;

class CreatorAddress extends Text {

    public function __construct($values) {
        parent::__construct($values + [
            'blankStyle' => 'hidden',
            'readonly' => true,
            'required' => true,
        ]);
    }

    public function generate($value) {
        if (defined('REMOTE_ADDR')) {
            return REMOTE_ADDR;
        }

        return null;
    }

}
