<?php //>

namespace matrix\db\column;

class Double {

    use type\Double;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'double',
            'searchStyle' => 'between',
            'validation' => 'double',
        ];
    }

}
