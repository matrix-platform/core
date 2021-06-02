<?php //>

namespace matrix\db\criterion;

use matrix\db\Criterion;

abstract class AbstractCriterion implements Criterion {

    protected $column;
    protected $language = LANGUAGE;
    protected $values;

    public function __construct($column, $values) {
        $this->column = $column;
        $this->values = array_map([$column, 'convert'], $values);
    }

    public function __call($name, $args) {
        return call_user_func_array([$this->and(), $name], $args);
    }

    public function and() {
        return Polymer::createAnd($this->column, $this);
    }

    public function bind($statement, $bindings) {
        $type = $this->column->type();

        foreach ($this->values as $value) {
            $bindings[] = $value;

            $statement->bindValue(count($bindings), $value, $type);
        }

        return $bindings;
    }

    public function make($dialect) {
        $expression = $this->column->expression($dialect, $this->language);

        return $this->build($dialect, $expression);
    }

    public function or() {
        return Polymer::createOr($this->column, $this);
    }

    public function with($language) {
        $this->language = $language;

        return $this;
    }

    abstract protected function build($dialect, $expression);

}
