<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Textarea {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'textarea',
            'searchStyle' => 'like',
        ];
    }

}
