<?php //>

namespace matrix\db\column;

class Junction {

    use type\Text {
        expression as private columnExpression;
    }

    private $column;

    public function __construct($name, $column) {
        $this->column = $column;
        $this->values = ['multiple' => true, 'name' => $name];
    }

    public function expression($dialect, $language = null, $prefix = null, $name = null, $select = false) {
        $expression = $this->columnExpression($dialect, $language, $prefix, $name, $select);

        return $select ? $dialect->makeImplodeExpression($expression, ',') : $expression;
    }

    public function mapping() {
        return $this->column->mapping();
    }

    public function options() {
        return $this->column->options();
    }

}
