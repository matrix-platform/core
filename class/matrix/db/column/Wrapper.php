<?php //>

namespace matrix\db\column;

use matrix\db\criterion\Helper;
use matrix\utility\ValueObject;

class Wrapper {

    use Helper, ValueObject;

    private $alias;
    private $relation;

    public function __construct($alias, $column, $relation) {
        $this->alias = $alias;
        $this->decorated = $column;
        $this->relation = $relation;
        $this->values = [];

        if (!$this->isJunction()) {
            $this->values['readonly'] = true;
        }
    }

    public function alias() {
        return $this->alias;
    }

    public function convert($value) {
        return $this->decorated->convert($value);
    }

    public function expression($dialect, $language = null, $prefix = null, $select = false) {
        return $this->decorated->expression($dialect, $language, $prefix ?: $this->alias, $select);
    }

    public function isCounter() {
        return ($this->decorated instanceof Counter);
    }

    public function isJunction() {
        return ($this->decorated instanceof Junction);
    }

    public function isWrapper() {
        return true;
    }

    public function relation() {
        return $this->relation;
    }

    public function type() {
        return $this->decorated->type();
    }

    public function validate($value) {
        return $this->decorated->validate($value);
    }

}
