<?php //>

namespace matrix\db\column;

class Color {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'color',
            'searchStyle' => false,
            'unordered' => true,
        ];
    }

}
