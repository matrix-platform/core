<?php //>

namespace matrix\db\column;

class Image {

    use type\File;

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
