<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Password {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'password',
            'searchStyle' => false,
            'unordered' => true,
        ];
    }

}
