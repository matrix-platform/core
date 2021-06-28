<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class File {

    use Column, type\File;

    public function __construct($values) {
        $this->values = $values + [
            'attachment' => true,
            'formStyle' => 'file',
            'unordered' => true,
            'validation' => 'file',
        ];
    }

}
