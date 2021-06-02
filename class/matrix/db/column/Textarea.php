<?php //>

namespace matrix\db\column;

class Textarea extends Text {

    public function __construct($values) {
        parent::__construct($values + ['formStyle' => 'textarea']);
    }

}
