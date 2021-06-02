<?php //>

namespace matrix\db\column;

class Url extends Text {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'url',
            'validation' => 'url',
        ]);
    }

}
