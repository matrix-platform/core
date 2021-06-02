<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Wrapper extends Column {

    private $alias;
    private $column;
    private $relation;

    public function __construct($alias, $column, $relation) {
        $this->alias = $alias;
        $this->column = $column;
        $this->relation = $relation;
        $this->values = $column->values;
    }

    public function alias() {
        return $this->alias;
    }

    public function convert($value) {
        return $this->column->convert($value);
    }

    public function expression($dialect, $language = null, $prefix = null) {
        return $this->column->expression($dialect, $language, $prefix ?? $this->alias);
    }

    public function generate($value) {
        return $this->column->generate($value);
    }

    public function isCounter() {
        return ($this->column instanceof Counter);
    }

    public function readonly() {
        return true;
    }

    public function regenerate($value) {
        return $this->column->regenerate($value);
    }

    public function relation() {
        return $this->relation;
    }

    public function type() {
        return $this->column->type();
    }

}
