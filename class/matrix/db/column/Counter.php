<?php //>

namespace matrix\db\column;

class Counter {

    use type\Integer {
        expression as private columnExpression;
    }

    public function __construct($values = []) {
        $this->values = $values + [
            'formStyle' => 'integer',
            'listStyle' => 'counter',
            'name' => 'count',
            'readonly' => true,
            'searchStyle' => 'between',
        ];
    }

    public function expression($dialect, $language = null, $prefix = null, $name = null, $select = false) {
        $expression = $this->columnExpression($dialect, $language, $prefix, $name, $select);

        return $select ? $dialect->makeDefaultExpression($expression, 0) : $expression;
    }

}
