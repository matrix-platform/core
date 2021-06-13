<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Modifier {

    use Column, type\Integer;

    public function __construct($values) {
        $this->values = $values + ['invisible' => true];
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
