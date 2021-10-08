<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use PDO;

trait DateTime {

    use Column {
        expression as private columnExpression;
    }

    public function convert($value) {
        return is_int($value) ? date($this->pattern(), $value) : $value;
    }

    public function expression($dialect, $language = null, $prefix = null, $select = false) {
        $expression = $this->columnExpression($dialect, $language, $prefix, $select);

        return $select ? $dialect->makeDateTimeExpression($expression, $this->pattern()) : $expression;
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
