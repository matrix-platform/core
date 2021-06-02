<?php //>

namespace matrix\db\column;

class Modifier extends Integer {

    public function __construct($values) {
        parent::__construct($values + ['invisible' => true]);
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

    public function regenerate($value) {
        return $this->generate($value);
    }

}
