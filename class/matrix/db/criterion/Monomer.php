<?php //>

namespace matrix\db\criterion;

use matrix\db\Criterion;

class Monomer implements Criterion {

    protected $column;
    protected $language = LANGUAGE;
    protected $operator;
    protected $values;

    public function __construct($column, $operator, $values) {
        $this->column = $column;
        $this->operator = $operator;
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

        return $dialect->{$this->operator}($expression, $this->values);
    }

    public function or() {
        return Polymer::createOr($this->column, $this);
    }

    public function with($language) {
        if ($language) {
            $this->language = $language;
        }

        return $this;
    }

}
