<?php //>

namespace matrix\db\column;

class Text {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'text',
            'searchStyle' => 'like',
        ];
    }

}
