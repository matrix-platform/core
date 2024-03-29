<?php //>

namespace matrix\db;

trait Dialect {

    public function between($expression, $values) {
        return "{$expression} BETWEEN ? AND ?";
    }

    public function equal($expression, $values) {
        return "{$expression} = ?";
    }

    public function greaterThan($expression, $values) {
        return "{$expression} > ?";
    }

    public function greaterThanOrEqual($expression, $values) {
        return "{$expression} >= ?";
    }

    public function iLike($expression, $values) {
        return "LOWER({$expression}) LIKE LOWER(?)";
    }

    public function in($expression, $values) {
        $count = count($values);

        switch ($count) {
        case 0:
            return '1 <> 1';
        case 1:
            return "{$expression} = ?";
        default:
            $params = implode(',', array_fill(0, $count, '?'));
            return "{$expression} IN ({$params})";
        }
    }

    public function isNull($expression, $values) {
        return "{$expression} IS NULL";
    }

    public function lessThan($expression, $values) {
        return "{$expression} < ?";
    }

    public function lessThanOrEqual($expression, $values) {
        return "{$expression} <= ?";
    }

    public function like($expression, $values) {
        return "{$expression} LIKE ?";
    }

    public function makeCountSelection($table, $criteria) {
        $command = "SELECT COUNT(*) FROM {$table->mapping()} AS _";

        if ($criteria->count()) {
            $command = $this->makeRelationJoin($command, $table, $criteria);
            $command = $this->makeCriteria($command, $criteria);
        }

        return $command;
    }

    public function makeCriteria($command, $criteria) {
        $conditions = $criteria->make($this);

        if ($conditions === false) {
            return $command;
        }

        return "{$command} WHERE {$conditions}";
    }

    abstract public function makeDateTimeExpression($expression, $pattern);

    abstract public function makeDefaultExpression($expression, $default);

    public function makeDeletion($table, $criteria) {
        $command = "DELETE FROM {$table->mapping()} AS _";

        return $this->makeCriteria($command, $criteria);
    }

    abstract public function makeImplodeExpression($expression, $separator);

    public function makeInsertion($table, $columns) {
        $expressions = [];

        foreach ($table->getColumns($columns) as $column) {
            if ($column->pseudo() || $column->isWrapper()) {
                continue;
            }

            $mapping = $column->mapping();

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $expressions[] = "{$mapping}__{$lang}";
                }
            } else {
                $expressions[] = $mapping;
            }
        }

        $names = implode(', ', $expressions);
        $values = implode(',', array_fill(0, count($expressions), '?'));

        if ($table->versionable()) {
            $names = "{$names}, __version__";
            $values = "{$values},1";
        }

        return "INSERT INTO {$table->mapping()} ({$names}) VALUES ({$values})";
    }

    abstract public function makeOrder($command, $columns, $orders);

    abstract public function makePager($command, $size, $page);

    public function makeSelection($table, $columns, $criteria, $orders, $select = true) {
        if (!is_array($orders)) {
            if ($orders === true) {
                $orders = [$table->ranking() ?: $table->id()];
            } else if (is_string($orders)) {
                $orders = [$orders];
            } else {
                $orders = null;
            }
        }

        $expressions = [];
        $multilinguals = [];
        $wrappers = [];

        foreach ($table->getColumns($columns, $orders) as $name => $column) {
            if ($column->pseudo()) {
                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $expression = $column->expression($this, $lang, null, null, $select);
                    $quoted = $this->quote("{$name}__{$lang}");

                    $expressions["{$name}__{$lang}"] = "{$expression} AS {$quoted}";
                }

                $multilinguals[$name] = true;
            } else {
                $expression = $column->expression($this, null, null, null, $select);
                $quoted = $this->quote($name);

                $expressions[$name] = "{$expression} AS {$quoted}";
            }

            if ($column->isWrapper()) {
                $wrappers[] = $column;
            }
        }

        $names = implode(', ', $expressions);

        if ($table->versionable()) {
            $quoted = $this->quote('__version__');
            $names = "{$names}, __version__ AS {$quoted}";
        }

        $command = "SELECT {$names} FROM {$table->mapping()} AS _";

        if ($wrappers || $criteria->count()) {
            $command = $this->makeRelationJoin($command, $table, $criteria);
            $command = $this->makeCriteria($command, $criteria);
        }

        if ($orders) {
            $command = $this->makeOrder($command, $expressions + $multilinguals, $orders);
        }

        return $command;
    }

    abstract public function makeToArrayExpression($expression);

    public function makeUpdation($table, $columns, $criteria) {
        $expressions = [];

        foreach ($table->getColumns($columns) as $column) {
            if ($column->pseudo() || $column->readonly() || $column->isWrapper()) {
                continue;
            }

            $mapping = $column->mapping();

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $expressions[] = "{$mapping}__{$lang} = ?";
                }
            } else {
                $expressions[] = "{$mapping} = ?";
            }
        }

        if ($table->versionable()) {
            $expressions[] = '__version__ = __version__ + 1';
        }

        $set = implode(', ', $expressions);

        $command = "UPDATE {$table->mapping()} AS _ SET {$set}";

        return $this->makeCriteria($command, $criteria);
    }

    public function notBetween($expression, $values) {
        return "{$expression} NOT BETWEEN ? AND ?";
    }

    public function notEqual($expression, $values) {
        return "{$expression} <> ?";
    }

    public function notILike($expression, $values) {
        return "LOWER({$expression}) NOT LIKE LOWER(?)";
    }

    public function notIn($expression, $values) {
        $count = count($values);

        switch ($count) {
        case 0:
            return false;
        case 1:
            return "{$expression} <> ?";
        default:
            $params = implode(',', array_fill(0, $count, '?'));
            return "{$expression} NOT IN ({$params})";
        }
    }

    public function notLike($expression, $values) {
        return "{$expression} NOT LIKE ?";
    }

    public function notNull($expression, $values) {
        return "{$expression} IS NOT NULL";
    }

    abstract public function overlap($expression, $values);

    abstract public function quote($name);

    private function makeRelationJoin($command, $table, $criteria) {
        foreach ($table->getRelations() as $alias => $relation) {
            if (!@$relation['enable']) {
                continue;
            }

            $column = $relation['column']->expression($this);
            $target = $relation['target']->mapping();

            if ($relation['type'] === 'composition') {
                $foreign = $relation['foreign']->mapping();

                if ($relation['junction']) {
                    $reference = $relation['reference']->mapping();
                    $aggregate = $this->makeToArrayExpression($reference);

                    $foreign = "SELECT {$target}, {$aggregate} AS {$reference} FROM {$foreign} GROUP BY {$target}";
                } else {
                    $foreign = "SELECT {$target}, COUNT(*) AS count FROM {$foreign} GROUP BY {$target}";
                }
            } else {
                $sub = Criteria::create($relation['foreign'], $relation['filter']);
                $foreign = $this->makeSelection($relation['foreign'], $relation['names'], $sub, [], false);

                $criteria->prepend($sub);
            }

            $command = "{$command} LEFT JOIN ({$foreign}) AS _{$alias} ON (_{$alias}.{$target} = {$column})";
        }

        return $command;
    }

}
