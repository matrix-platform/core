<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class ModifierAddress {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'disabled' => true,
            'formStyle' => 'text',
            'searchStyle' => 'like',
        ];
    }

    public function generate($value) {
        return constant('REMOTE_ADDR');
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
