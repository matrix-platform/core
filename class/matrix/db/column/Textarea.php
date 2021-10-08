<?php //>

namespace matrix\db\column;

class Textarea {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'textarea',
            'searchStyle' => 'like',
        ];
    }

}
