<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Image {

    use Column, type\File;

    public function __construct($values) {
        $this->values = $values + [
            'attachment' => true,
            'formStyle' => 'image',
            'searchStyle' => false,
            'unordered' => true,
            'validation' => 'image',
        ];
    }

}
