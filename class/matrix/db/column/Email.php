<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Email {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'email',
            'searchStyle' => 'like',
            'validation' => 'email',
        ];
    }

}
