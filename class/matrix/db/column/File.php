<?php //>

namespace matrix\db\column;

class File {

    use type\File;

    public function __construct($values) {
        $this->values = $values + [
            'attachment' => true,
            'formStyle' => 'file',
            'unordered' => true,
            'validation' => 'file',
        ];
    }

}
