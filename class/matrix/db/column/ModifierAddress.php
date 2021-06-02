<?php //>

namespace matrix\db\column;

class ModifierAddress extends Text {

    public function __construct($values) {
        parent::__construct($values + [
            'blankStyle' => 'hidden',
            'disabled' => true,
        ]);
    }

    public function generate($value) {
        if (defined('REMOTE_ADDR')) {
            return REMOTE_ADDR;
        }

        return null;
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
