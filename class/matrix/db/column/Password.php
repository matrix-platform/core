<?php //>

namespace matrix\db\column;

class Password {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'password',
            'listStyle' => 'hidden',
            'searchStyle' => false,
            'unordered' => true,
        ];
    }

}
