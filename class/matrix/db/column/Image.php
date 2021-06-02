<?php //>

namespace matrix\db\column;

class Image extends File {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'image',
            'mimeType' => 'image\/[\w]+',
            'validation' => 'image',
        ]);
    }

}
