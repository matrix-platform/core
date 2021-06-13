<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Text {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'text',
            'searchStyle' => 'like',
        ];
    }

}
