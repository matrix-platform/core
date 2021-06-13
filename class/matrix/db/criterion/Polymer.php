<?php //>

namespace matrix\db\criterion;

use Exception;
use matrix\db\Criteria;
use matrix\db\Criterion;
use ReflectionClass;

class Polymer implements Criterion {

    public static function createAnd($column, $criterion) {
        return new self($column, Criteria::createAnd($criterion));
    }

    public static function createOr($column, $criterion) {
        return new self($column, Criteria::createOr($criterion));
    }

    private $column;
    private $criteria;
    private $criterion;

    private function __construct($column, $criteria) {
        $this->column = $column;
        $this->criteria = $criteria;
    }

    public function __call($name, $args) {
        if (!$this->supported($name)) {
            throw new Exception('Unsupported operation.');
        }

        if ($this->criterion) {
            return call_user_func_array([$this->and(), $name], $args);
        }

        $this->criterion = call_user_func_array([$this->column, $name], $args);
        $this->criteria->add($this->criterion);

        return $this;
    }

    public function and() {
        return self::createAnd($this->column, $this->criteria);
    }

    public function bind($statement, $bindings) {
        return $this->criteria->bind($statement, $bindings);
    }

    public function make($dialect) {
        return $this->criteria->make($dialect);
    }

    public function or() {
        return self::createOr($this->column, $this->criteria);
    }

    public function with($language) {
        return $this->criteria->with($language);
    }

    private function supported($name) {
        static $methods;

        if (!$methods) {
            $helper = new ReflectionClass(Helper::class);
            $methods = array_column($helper->getMethods(), 'class', 'name');
        }

        return key_exists($name, $methods);
    }

}
