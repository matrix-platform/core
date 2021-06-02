<?php //>

namespace matrix\db\column;

class Password extends Text {

    public function __construct($values) {
        parent::__construct($values + ['formStyle' => 'password']);
    }

}
