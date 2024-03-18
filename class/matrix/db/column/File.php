<?php //>

namespace matrix\db\column;

class File {

    use type\File;

    public function __construct($values) {
        $this->values = $values + [
            'attachment' => true,
            'filename' => '{{ _file.name }}',
            'formStyle' => 'file',
            'unordered' => true,
            'validation' => 'file',
        ];
    }

}
