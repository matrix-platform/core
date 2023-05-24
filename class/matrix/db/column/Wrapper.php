<?php //>

namespace matrix\db\column;

use matrix\db\criterion\Helper;
use matrix\utility\ValueObject;

class Wrapper {

    use Helper, ValueObject;

    protected static $defaults = ['i18n' => 'internationalization'];

    private $alias;
    private $name;
    private $relation;
    private $table;

    public function __construct($name, $alias, $table, $column, $relation) {
        $this->name = $name;
        $this->alias = $alias;
        $this->decorated = $column;
        $this->relation = $relation;
        $this->table = $table;
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

    public function expression($dialect, $language = null, $prefix = null, $name = null, $select = false) {
        if ($this->decorated->isWrapper() && !$name) {
            $name = $this->decorated->name;
        }

        return $this->decorated->expression($dialect, $language, $prefix ?: $this->alias, $name, $select);
    }

    public function internationalization() {
        if ($this->isCounter()) {
            return "table/{$this->table->name()}.{$this->name}";
        } else {
            return $this->decorated->i18n();
        }
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
