<?php //>

namespace matrix\db\column;

class Url {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'url',
            'searchStyle' => 'like',
            'validation' => 'url',
        ];
    }

}
