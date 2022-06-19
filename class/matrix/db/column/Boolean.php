<?php //>

namespace matrix\db\column;

class Boolean {

    use type\Boolean;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'radio',
            'options' => load_options('yes-no'),
            'validation' => 'boolean',
        ];
    }

}
