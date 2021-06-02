<?php //>

namespace matrix\db\column;

class Email extends Text {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'email',
            'validation' => 'email',
        ]);
    }

}
