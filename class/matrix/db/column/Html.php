<?php //>

namespace matrix\db\column;

class Html extends Text {

    public function __construct($values) {
        parent::__construct($values + [
            'formStyle' => 'html',
            'unordered' => true,
        ]);
    }

}
