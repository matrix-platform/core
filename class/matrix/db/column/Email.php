<?php //>

namespace matrix\db\column;

class Email {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'email',
            'searchStyle' => 'like',
            'validation' => 'email',
        ];
    }

}
