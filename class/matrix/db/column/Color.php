<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Color {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'color',
            'searchStyle' => false,
            'unordered' => true,
        ];
    }

}
