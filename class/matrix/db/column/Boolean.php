<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Boolean {

    use Column, type\Boolean;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'radio',
            'options' => load_options('yes-no'),
        ];
    }

}
