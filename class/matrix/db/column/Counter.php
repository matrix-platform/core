<?php //>

namespace matrix\db\column;

class Counter extends Integer {

    public function __construct($values = []) {
        parent::__construct($values + [
            'formStyle' => 'counter',
            'name' => 'count',
        ]);
    }

    public function expression($dialect, $language = null, $prefix = null) {
        $expression = parent::expression($dialect, $language, $prefix);

        return $dialect->makeDefaultExpression($expression, 0);
    }

}
