<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Counter {

    use Column, type\Integer {
        expression as private columnExpression;
    }

    public function __construct($values = []) {
        $this->values = $values + [
            'formStyle' => 'counter',
            'name' => 'count',
            'searchStyle' => 'between',
        ];
    }

    public function expression($dialect, $language = null, $prefix = null, $select = false) {
        $expression = $this->columnExpression($dialect, $language, $prefix, $select);

        return $dialect->makeDefaultExpression($expression, 0);
    }

}
