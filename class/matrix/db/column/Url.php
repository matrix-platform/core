<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Url {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'url',
            'searchStyle' => 'like',
            'validation' => 'url',
        ];
    }

}
