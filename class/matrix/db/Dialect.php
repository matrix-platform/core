<?php //>

namespace matrix\db;

abstract class Dialect {

    public function makeCountSelection($table, $criteria) {
        $command = "SELECT COUNT(*) FROM {$table->mapping()} AS _";
        $command = $this->makeRelationJoin($command, $table);

        return $this->makeCriteria($command, $criteria);
    }

    public function makeCriteria($command, $criteria) {
        $conditions = $criteria->make($this);

        if ($conditions === false) {
            return $command;
        }

        return "{$command} WHERE {$conditions}";
    }

    abstract public function makeDefaultExpression($expression, $default);

    public function makeDeletion($table, $criteria) {
        $command = "DELETE FROM {$table->mapping()} AS _";

        return $this->makeCriteria($command, $criteria);
    }

    public function makeInsertion($table, $columns) {
        $expressions = [];

        foreach ($columns ?: $table->getColumns() as $column) {
            if ($column->pseudo()) {
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

    public function makeOrder($command, $columns, $orders) {
        $expressions = [];

        foreach ($orders as $name) {
            if ($name === '?') {
                $expressions[] = $this->makeRandom();
            } else {
                if ($name[0] === '-') {
                    $name = substr($name, 1);
                    $type = 'DESC';
                } else {
                    $type = 'ASC';
                }

                if (key_exists($name, $columns)) {
                    if ($columns[$name] === true) {
                        $name = $name . '__' . LANGUAGE;
                    }

                    $quoted = $this->quote($name);
                    $expressions[] = "{$quoted} {$type}";
                }
            }
        }

        if ($expressions) {
            $order = implode(', ', $expressions);

            return "{$command} ORDER BY {$order}";
        }

        return $command;
    }

    abstract public function makePager($command, $size, $page);

    abstract public function makeRandom();

    public function makeSelection($table, $columns, $criteria, $orders) {
        $expressions = [];
        $multilinguals = [];

        foreach ($columns ?: $table->getColumns() as $name => $column) {
            if ($column->pseudo()) {
                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $expression = $column->expression($this, $lang);
                    $quoted = $this->quote("{$name}__{$lang}");

                    $expressions["{$name}__{$lang}"] = "{$expression} AS {$quoted}";
                }

                $multilinguals[$name] = true;
            } else {
                $expression = $column->expression($this);
                $quoted = $this->quote($name);

                $expressions[$name] = "{$expression} AS {$quoted}";
            }
        }

        $names = implode(', ', $expressions);

        if ($table->versionable()) {
            $quoted = $this->quote('__version__');
            $names = "{$names}, __version__ AS {$quoted}";
        }

        $command = "SELECT {$names} FROM {$table->mapping()} AS _";
        $command = $this->makeRelationJoin($command, $table);
        $command = $this->makeCriteria($command, $criteria);

        if ($orders) {
            if ($orders === true) {
                $orders = [$table->ranking() ?: $table->id()];
            }

            $command = $this->makeOrder($command, $expressions + $multilinguals, $orders);
        }

        return $command;
    }

    public function makeUpdation($table, $columns, $criteria) {
        $expressions = [];

        foreach ($columns ?: $table->getColumns() as $column) {
            if ($column->pseudo() || $column->readonly()) {
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

    abstract public function quote($name);

    private function makeRelationJoin($command, $table) {
        foreach ($table->getRelations() as $alias => $relation) {
            if (!@$relation['enable']) {
                continue;
            }

            $column = $relation['column']->expression($this);
            $foreign = $relation['foreign']->mapping();
            $target = $relation['target']->mapping();

            if ($relation['type'] === 'composition') {
                $foreign = "(SELECT {$target}, COUNT(*) AS count FROM {$foreign} GROUP BY {$target})";
            }

            $command = "{$command} LEFT JOIN {$foreign} AS _{$alias} ON (_{$alias}.{$target} = {$column})";
        }

        return $command;
    }

}
