<?php //>

namespace matrix\db;

use Closure;
use matrix\db\criterion\Version;
use PDO;

class Model {

    const INSERT = 1;
    const UPDATE = 2;
    const DELETE = 3;

    private static $administration = false;

    public static function enableAdministration() {
        self::$administration = true;
    }

    protected $cache;
    protected $db;
    protected $dialect;
    protected $filter;
    protected $table;

    public function __construct($db, $table) {
        $this->cache = $db->createCache();
        $this->db = $db;
        $this->dialect = $db->dialect();
        $this->filter = !self::$administration;
        $this->table = $table;
    }

    public function count($conditions = []) {
        $criteria = $this->createCriteria($conditions, $this->filter);
        $command = $this->dialect->makeCountSelection($this->table, $criteria);
        $statement = $this->db->prepare($command);

        $this->execute($statement, $criteria->bind($statement, []));

        return $statement->fetchColumn();
    }

    public function delete($data) {
        $previous = $this->get(is_array($data) ? $data['id'] : $data);

        if (!$previous) {
            return null;
        }

        $this->before(self::DELETE, $previous, null);

        $criteria = $this->createCriteria(['id' => $previous['id']]);
        $command = $this->dialect->makeDeletion($this->table, $criteria);
        $statement = $this->db->prepare($command);

        $this->execute($statement, $criteria->bind($statement, []));

        $this->cache->remove($previous['id']);

        if ($statement->rowCount() !== 1) {
            return false;
        }

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->isJunction() && isset($previous[$name])) {
                $this->deleteJunction($column, $previous);
            }
        }

        $this->log($previous, null);
        $this->after(self::DELETE, $previous, null);

        return $previous;
    }

    public function enableFilter($filter = true) {
        $this->filter = $filter;

        return $this;
    }

    public function find($conditions) {
        $result = $this->query($conditions);

        if (count($result) !== 1) {
            return null;
        }

        return $result[0];
    }

    public function get($id) {
        if ($this->db->cacheable() && $this->table->cacheable()) {
            $data = $this->cache->get($id);

            if ($data) {
                return $data;
            }
        }

        return $this->find(['id' => $id]);
    }

    public function insert($data) {
        $junctions = [];

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->pseudo()) {
                continue;
            }

            if ($column->isJunction()) {
                if (isset($data[$name])) {
                    $junctions[] = $column;
                }

                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $prop = "{$name}__{$lang}";
                    $data[$prop] = $this->forInsert($column, $data, $prop);
                }
            } else {
                $data[$name] = $this->forInsert($column, $data, $name);
            }
        }

        $data = $this->before(self::INSERT, null, $data);

        $bindings = [];
        $command = $this->dialect->makeInsertion($this->table, false);
        $statement = $this->db->prepare($command);

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->pseudo() || $column->isJunction()) {
                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $value = $data["{$name}__{$lang}"];
                    $bindings[] = $value;

                    $statement->bindValue(count($bindings), $value, $column->type());
                }
            } else {
                $value = $data[$name];
                $bindings[] = $value;

                $statement->bindValue(count($bindings), $value, $column->type());
            }
        }

        $this->execute($statement, $bindings);

        if ($statement->rowCount() !== 1) {
            return false;
        }

        foreach ($junctions as $column) {
            $this->insertJunction($column, $data);
        }

        $current = $this->get($data['id']);

        if ($current) {
            $this->log(null, $current);
            $this->after(self::INSERT, null, $current);
        }

        return $current;
    }

    public function parents($data) {
        if ($data !== null) {
            $relation = $this->table->getParentRelation();

            if ($relation) {
                $value = $data[$relation['column']->name()];

                if ($value !== null) {
                    $model = $relation['foreign']->model();
                    $parent = $model->find([$relation['target']->equal($value)]);

                    if ($parent) {
                        $parent['.title'] = $model->toString($parent);

                        $parents = $model->parents($parent);
                        $parents[] = $parent;

                        return $parents;
                    }
                }
            }
        }

        return [];
    }

    public function query($conditions = [], $orders = true, $size = 0, $page = 1, $columns = false) {
        $criteria = $this->createCriteria($conditions, $this->filter);
        $command = $this->dialect->makeSelection($this->table, $columns, $criteria, $orders);

        if ($size > 0 && $page > 0) {
            $command = $this->dialect->makePager($command, $size, $page);
        }

        $statement = $this->db->prepare($command);

        $this->execute($statement, $criteria->bind($statement, []));

        return $this->fetch($statement, $columns);
    }

    public function toString($data) {
        $title = $this->table->title() ?: 'title';

        if ($title instanceof Closure) {
            return call_user_func($title, $data);
        }

        return isset($this->table->{$title}) ? "{$data[$title]}" : null;
    }

    public function update($data) {
        $previous = $this->get($data['id']);

        if (!$previous) {
            return null;
        }

        $junctions = [];

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->pseudo() || $column->readonly()) {
                continue;
            }

            if ($column->isJunction()) {
                if (key_exists($name, $data) && $previous[$name] !== $data[$name]) {
                    $junctions[$name] = $column;
                }

                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $prop = "{$name}__{$lang}";
                    $data[$prop] = $this->forUpdate($column, $data, $prop, $previous);
                }
            } else {
                $data[$name] = $this->forUpdate($column, $data, $name, $previous);
            }
        }

        $data = $this->before(self::UPDATE, $previous, $data);

        $bindings = [];
        $conditions = ['id' => $previous['id']];

        if ($this->table->versionable()) {
            $conditions[] = new Version($data['__version__']);
        }

        $criteria = $this->createCriteria($conditions);
        $command = $this->dialect->makeUpdation($this->table, false, $criteria);
        $statement = $this->db->prepare($command);

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->pseudo() || $column->readonly() || $column->isJunction()) {
                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $value = $data["{$name}__{$lang}"];
                    $bindings[] = $value;

                    $statement->bindValue(count($bindings), $value, $column->type());
                }
            } else {
                $value = $data[$name];
                $bindings[] = $value;

                $statement->bindValue(count($bindings), $value, $column->type());
            }
        }

        $this->execute($statement, $criteria->bind($statement, $bindings));

        $this->cache->remove($previous['id']);

        if ($statement->rowCount() !== 1) {
            return false;
        }

        foreach ($junctions as $name => $column) {
            if ($previous[$name]) {
                $this->deleteJunction($column, $previous);
            }

            if ($data[$name]) {
                $this->insertJunction($column, $data);
            }
        }

        $current = $this->get($previous['id']);

        if ($current) {
            $this->log($previous, $current);
            $this->after(self::UPDATE, $previous, $current);
        }

        return $current;
    }

    protected function after($type, $prev, $curr) {
    }

    protected function before($type, $prev, $curr) {
        return $curr;
    }

    protected function execute($statement, $bindings) {
        logging('sql')->debug($statement->queryString, $bindings);

        $statement->execute();
    }

    private function cleanup($data) {
        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->multilingual()) {
                unset($data[$name]);
            }
        }

        return $data;
    }

    private function createCriteria($conditions, $filter = false) {
        if ($conditions instanceof Closure) {
            $conditions = call_user_func($conditions, $this->table);
        }

        $criteria = Criteria::createAnd();

        foreach ($conditions as $name => $value) {
            if ($value instanceof Criterion) {
                $criteria->add($value);
            } else if (isset($this->table->{$name})) {
                if ($value === null) {
                    $criteria->add($this->table->{$name}->isNull());
                } else if (is_array($value)) {
                    $criteria->add($this->table->{$name}->in($value));
                } else {
                    $criteria->add($this->table->{$name}->equal($value));
                }
            }
        }

        if ($filter) {
            $enable = $this->table->enableTime();

            if ($enable) {
                $column = $this->table->{$enable};
                $now = date($column->pattern());

                $criteria->add($column->notNull()->lessThanOrEqual($now));
            }

            $disable = $this->table->disableTime();

            if ($disable) {
                $column = $this->table->{$disable};
                $now = date($column->pattern());

                $criteria->add($column->isNull()->or()->greaterThan($now));
            }
        }

        return $criteria;
    }

    private function deleteJunction($column, $data) {
        $relation = $column->relation();
        $model = $relation['foreign']->model();
        $from = $relation['target']->name();
        $id = $data[$relation['column']->name()];

        foreach ($model->query([$from => $id]) as $row) {
            $model->delete($row);
        }
    }

    private function fetch($statement, $columns) {
        $rows = $statement->fetchAll();

        foreach ($this->table->getColumns($columns) as $name => $column) {
            if ($column->pseudo()) {
                continue;
            }

            if ($column->multilingual()) {
                $local = $name . '__' . LANGUAGE;

                foreach ($rows as &$row) {
                    $row[$name] = $row[$local];
                }
            }
        }

        if ($columns === false && $this->db->cacheable() && $this->table->cacheable()) {
            array_walk($rows, [$this->cache, 'put']);
        }

        return $rows;
    }

    private function forInsert($column, $data, $name) {
        $value = @$data[$name];

        if ($value === null) {
            $value = $column->default();
        } else {
            $value = $column->convert($value);
        }

        return $column->generate($value);
    }

    private function forUpdate($column, $data, $name, $previous) {
        if (key_exists($name, $data)) {
            $value = $data[$name];

            if ($value !== null) {
                $value = $column->convert($value);
            }
        } else {
            $value = $previous[$name];
        }

        return $column->regenerate($value);
    }

    private function insertJunction($column, $data) {
        $relation = $column->relation();
        $model = $relation['foreign']->model();
        $from = $relation['target']->name();
        $id = $data[$relation['column']->name()];
        $to = $relation['reference']->name();

        foreach (explode(',', $data[$column->name()]) as $value) {
            $model->insert([$from => $id, $to => $value]);
        }
    }

    private function log($prev, $curr) {
        if (!$this->table->traceable()) {
            return;
        }

        if ($prev) {
            if ($curr) {
                $diff = [];

                foreach ($this->table->getColumns(false) as $name => $column) {
                    if ($column->pseudo() || $column->readonly()) {
                        continue;
                    }

                    if ($column->multilingual()) {
                        foreach (LANGUAGES as $lang) {
                            $prop = "{$name}__{$lang}";

                            if ($prev[$prop] !== $curr[$prop]) {
                                $diff[$prop] = $curr[$prop];
                            }
                        }
                    } else {
                        if ($prev[$name] !== $curr[$name]) {
                            $diff[$name] = $curr[$name];
                        }
                    }
                }

                $type = self::UPDATE;
                $curr = $diff ? json_encode($diff, JSON_UNESCAPED_UNICODE) : '{}';
            } else {
                $type = self::DELETE;
                $curr = null;
            }

            $dataId = $prev['id'];
            $prev = json_encode($this->cleanup($prev), JSON_UNESCAPED_UNICODE);
        } else {
            if ($curr) {
                $type = self::INSERT;
                $dataId = $curr['id'];
                $prev = null;
                $curr = json_encode($this->cleanup($curr), JSON_UNESCAPED_UNICODE);
            } else {
                return;
            }
        }

        $statement = $this->db->prepare('INSERT INTO base_manipulation_log (type,controller,user_id,member_id,ip,data_type,data_id,previous,current) VALUES (?,?,?,?,?,?,?,?,?)');
        $statement->bindValue(1, $type, PDO::PARAM_INT);
        $statement->bindValue(2, constant('CONTROLLER'), PDO::PARAM_STR);
        $statement->bindValue(3, @constant('USER_ID'), PDO::PARAM_INT);
        $statement->bindValue(4, @constant('MEMBER_ID') ?: @constant('VENDOR_ID'), PDO::PARAM_INT);
        $statement->bindValue(5, constant('REMOTE_ADDR'), PDO::PARAM_STR);
        $statement->bindValue(6, $this->table->name(), PDO::PARAM_STR);
        $statement->bindValue(7, $dataId, PDO::PARAM_INT);
        $statement->bindValue(8, $prev, PDO::PARAM_STR);
        $statement->bindValue(9, $curr, PDO::PARAM_STR);
        $statement->execute();
    }

}
