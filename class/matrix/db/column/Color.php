<?php //>

namespace matrix\db\column;

class Color extends Text {

    public function __construct($values) {
        parent::__construct($values + ['formStyle' => 'color']);
    }

}
