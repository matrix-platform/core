<?php //>

namespace matrix\db;

use Exception;
use matrix\db\column\Counter;
use matrix\db\column\Id;
use matrix\db\column\Wrapper;
use matrix\utility\ValueObject;

class Table {

    use ValueObject;

    private $columns = [];
    private $names = [];
    private $parent;
    private $relations = [];

    public function __construct($mapping, $traceable = true, $namespace = 'matrix\model') {
        $this->values = [
            'mapping' => $mapping,
            'namespace' => $namespace,
            'traceable' => $traceable,
        ];

        $this->add('id', Id::class);
    }

    public function __get($name) {
        if (key_exists($name, $this->columns)) {
            return $this->columns[$name];
        }

        throw new Exception("Column `{$this->name()}`.`{$name}` not found.");
    }

    public function __isset($name) {
        return key_exists($name, $this->columns);
    }

    public function __set($name, $value) {
        throw new Exception('Unsupported operation.');
    }

    public function __unset($name) {
        throw new Exception('Unsupported operation.');
    }

    public function add($name, $typeName) {
        if (key_exists($name, $this->columns)) {
            throw new Exception("Column `{$this->name()}`.`{$name}` exists.");
        }

        if (class_exists($typeName)) {
            $column = new $typeName(['name' => $name, 'table' => $this]);

            $this->names[] = $name;
        } else {
            list($alias, $column) = preg_split('/\./', $typeName);

            $relation = $this->getRelation($alias);

            if ($relation['type'] === 'composition') {
                switch ($column) {
                case 'count':
                    $column = new Counter();
                    break;
                default:
                    throw new Exception("Unsupported \$typeName `{$typeName}`.");
                }

                $this->names[] = $name;
            } else {
                $column = $relation['foreign']->{$column};

                array_splice($this->names, array_search($relation['column']->name(), $this->names), 0, $name);
            }

            $column = new Wrapper($alias, $column, $relation);

            $this->relations[$alias]['enable'] = true;
        }

        $this->columns[$name] = $column;

        return $column;
    }

    public function getColumns($names = null) {
        if ($names === false) {
            return $this->columns;
        }

        $columns = [];

        foreach ($names ?: $this->names as $name) {
            if (key_exists($name, $this->columns)) {
                $columns[$name] = $this->columns[$name];
            }
        }

        return $columns;
    }

    public function getComposition($table) {
        foreach ($this->relations as $alias => ['foreign' => $foreign, 'type' => $type]) {
            if ($type === 'composition') {
                if ($foreign === $table || $foreign === $table->name()) {
                    return $this->getRelation($alias);
                }
            }
        }

        return null;
    }

    public function getParentRelation() {
        if (is_string($this->parent)) {
            $this->parent = $this->getRelation($this->parent);
        }

        return $this->parent;
    }

    public function getRelation($alias) {
        if (!key_exists($alias, $this->relations)) {
            throw new Exception("Relation `{$alias}` of `{$this->name()}` not found.");
        }

        $relation = $this->relations[$alias];

        if (is_string($relation['foreign'])) {
            $foreign = table($relation['foreign']);

            if ($relation['type'] === 'composition' && !$relation['target']) {
                $reverse = $foreign->getParentRelation();

                if ($reverse && $reverse['foreign']->name() === $this->name()) {
                    $relation['target'] = $reverse['column']->name();
                }
            }

            $relation['foreign'] = $foreign;
            $relation['target'] = $foreign->{$relation['target']};
            $relation['self-referencing'] = $foreign === $this;

            $this->relations[$alias] = $relation;
        }

        return $relation;
    }

    public function getRelations() {
        array_map([$this, 'getRelation'], array_keys($this->relations));

        return $this->relations;
    }

    public function model() {
        return db()->model($this);
    }

    public function parent() {
        $relation = $this->getParentRelation();

        return $relation ? $relation['foreign'] : null;
    }

    public function register($relation) {
        $alias = $relation['alias'];

        if (key_exists($alias, $this->relations)) {
            throw new Exception("Relation `{$alias}` of `{$this->name()}` exists.");
        }

        $this->relations[$alias] = $relation;

        if (@$relation['parent'] && $this->parent !== false) {
            $this->parent = $this->parent ? false : $alias;
        }
    }

}
