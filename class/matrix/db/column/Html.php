<?php //>

namespace matrix\db\column;

class Html {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'html',
            'searchStyle' => false,
            'unordered' => true,
        ];
    }

}
