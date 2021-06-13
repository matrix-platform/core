<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Image {

    use Column, type\File;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'image',
            'mimeType' => 'image\/[\w]+',
            'unordered' => true,
            'validation' => 'image',
        ];
    }

}
