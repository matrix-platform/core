<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Html {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'html',
            'searchStyle' => false,
            'unordered' => true,
        ];
    }

}
