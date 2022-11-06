<?php //>

namespace matrix\db;

class Criteria implements Criterion {

    public static function create($table, $conditions, $filter = false) {
        if ($conditions instanceof Closure) {
            $conditions = call_user_func($conditions, $table);
        }

        $criteria = static::createAnd();

        foreach ($conditions as $name => $value) {
            if ($value instanceof Criterion) {
                $criteria->add($value);
            } else {
                if (str_contains($name, '.')) {
                    $alias = str_replace('.', '_', $name);

                    if (!isset($table->{$alias})) {
                        $table->add($alias, $name);
                    }

                    $name = $alias;
                }

                if (isset($table->{$name})) {
                    if ($value === null) {
                        $criteria->add($table->{$name}->isNull());
                    } else if (is_array($value)) {
                        $criteria->add($table->{$name}->in($value));
                    } else {
                        $criteria->add($table->{$name}->equal($value));
                    }
                }
            }
        }

        if ($filter) {
            $enable = $table->enableTime();

            if ($enable) {
                $column = $table->{$enable};
                $now = date($column->pattern());

                $criteria->add($column->notNull()->lessThanOrEqual($now));
            }

            $disable = $table->disableTime();

            if ($disable) {
                $column = $table->{$disable};
                $now = date($column->pattern());

                $criteria->add($column->isNull()->or()->greaterThan($now));
            }
        }

        return $criteria;
    }

    public static function createAnd(...$criteria) {
        return new Criteria($criteria, ' AND ');
    }

    public static function createOr(...$criteria) {
        return new Criteria($criteria, ' OR ');
    }

    private $criteria;
    private $operator;
    private $prepends = [];

    private function __construct($criteria, $operator) {
        $this->criteria = $criteria;
        $this->operator = $operator;
    }

    public function add($criterion) {
        $this->criteria[] = $criterion;

        return $this;
    }

    public function bind($statement, $bindings) {
        foreach ($this->prepends as $criteria) {
            $bindings = $criteria->bind($statement, $bindings);
        }

        foreach ($this->criteria as $criterion) {
            $bindings = $criterion->bind($statement, $bindings);
        }

        return $bindings;
    }

    public function count() {
        return count($this->criteria) + count($this->prepends);
    }

    public function make($dialect) {
        $expressions = [];

        foreach ($this->criteria as $criterion) {
            $expression = $criterion->make($dialect);

            if ($expression !== false) {
                $expressions[] = $expression;
            }
        }

        if ($expressions) {
            return '(' . implode($this->operator, $expressions) . ')';
        }

        return false;
    }

    public function prepend($criteria) {
        $this->prepends[] = $criteria;
    }

    public function with($language) {
        foreach ($this->criteria as $criterion) {
            $criterion->with($language);
        }

        return $this;
    }

}
