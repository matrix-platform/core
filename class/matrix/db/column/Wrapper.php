<?php //>

namespace matrix\db\column;

use matrix\utility\ValueObject;

class Wrapper {

    use ValueObject;

    private $alias;
    private $relation;

    public function __construct($alias, $column, $relation) {
        $this->alias = $alias;
        $this->decorated = $column;
        $this->relation = $relation;
        $this->values = [];
    }

    public function alias() {
        return $this->alias;
    }

    public function expression($dialect, $language = null, $prefix = null) {
        return $this->decorated->expression($dialect, $language, $prefix ?: $this->alias);
    }

    public function isCounter() {
        return ($this->decorated instanceof Counter);
    }

    public function readonly() {
        return true;
    }

    public function relation() {
        return $this->relation;
    }

}
