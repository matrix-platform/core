<?php //>

namespace matrix\db\column;

class Wrapper {

    private $alias;
    private $column;
    private $relation;

    public function __construct($alias, $column, $relation) {
        $this->alias = $alias;
        $this->column = $column;
        $this->relation = $relation;
    }

    public function __call($name, $args) {
        return call_user_func_array([$this->column, $name], $args);
    }

    public function alias() {
        return $this->alias;
    }

    public function expression($dialect, $language = null, $prefix = null) {
        return $this->column->expression($dialect, $language, $prefix ?: $this->alias);
    }

    public function isCounter() {
        return ($this->column instanceof Counter);
    }

    public function readonly() {
        return true;
    }

    public function relation() {
        return $this->relation;
    }

}
