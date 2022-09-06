<?php //>

namespace matrix\db\column;

class Integer {

    use type\Integer;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'integer',
            'searchStyle' => 'between',
            'validation' => 'integer|integer.max|integer.min',
        ];
    }

}
