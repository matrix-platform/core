<?php //>

namespace matrix\db\column;

class Creator extends Integer {

    public function __construct($values) {
        parent::__construct($values + [
            'invisible' => true,
            'readonly' => true,
            'required' => true,
        ]);
    }

    public function generate($value) {
        if (defined('USER_ID')) {
            return USER_ID;
        }

        if (defined('MEMBER_ID')) {
            return MEMBER_ID;
        }

        return null;
    }

}
