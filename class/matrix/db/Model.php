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
        $criteria = Criteria::create($this->table, $conditions, $this->filter);
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

        $criteria = Criteria::create($this->table, ['id' => $previous['id']]);
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

    public function history($id) {
        $command = 'SELECT A.id,' .
                         ' A.type,' .
                         ' A.log_time,' .
                         ' A.controller,' .
                         ' A.user_id,' .
                         ' B.username AS user,' .
                         ' A.member_id,' .
                         ' C.username AS member,' .
                         ' A.ip,' .
                         ' A.previous,' .
                         ' A.current' .
                    ' FROM base_manipulation_log AS A' .
               ' LEFT JOIN base_user AS B ON (A.user_id = B.id)' .
               ' LEFT JOIN common_member AS C ON (A.member_id = C.id)' .
                   ' WHERE A.data_type = ?' .
                     ' AND A.data_id = ?' .
                ' ORDER BY A.id DESC';

        $name = $this->table->name();

        $statement = $this->db->prepare($command);
        $statement->bindValue(1, $name, PDO::PARAM_STR);
        $statement->bindValue(2, $id, PDO::PARAM_INT);

        $this->execute($statement, [$name, $id]);

        $rows = [];

        foreach ($statement->fetchAll() as $row) {
            if ($row['previous']) {
                $row['previous'] = json_decode($row['previous'], true);
            }

            if ($row['current']) {
                $row['current'] = json_decode($row['current'], true);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function insert($data) {
        $junctions = [];

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->pseudo()) {
                continue;
            }

            if ($column->isWrapper()) {
                if ($column->isJunction() && isset($data[$name])) {
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
            if ($column->pseudo() || $column->isWrapper()) {
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
                $value = @$data[$relation['column']->name()];

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
        $criteria = Criteria::create($this->table, $conditions, $this->filter);
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

            if ($column->isWrapper()) {
                if ($column->isJunction() && key_exists($name, $data) && $previous[$name] !== $data[$name]) {
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

        $diff = $this->compare($previous, $data);

        if ($diff) {
            $bindings = [];
            $conditions = ['id' => $previous['id']];

            if ($this->table->versionable()) {
                $conditions[] = new Version($data['__version__']);
            }

            $criteria = Criteria::create($this->table, $conditions);
            $command = $this->dialect->makeUpdation($this->table, $diff, $criteria);
            $statement = $this->db->prepare($command);

            foreach ($this->table->getColumns($diff) as $name => $column) {
                if ($column->pseudo() || $column->readonly() || $column->isWrapper()) {
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

        unset($data[$this->table->id()]);

        return $data;
    }

    private function compare($prev, $curr) {
        $diff = [];

        foreach ($this->table->getColumns(false) as $name => $column) {
            if ($column->pseudo() || $column->readonly() || $column->isWrapper()) {
                continue;
            }

            if ($column->multilingual()) {
                foreach (LANGUAGES as $lang) {
                    $prop = "{$name}__{$lang}";

                    if ($prev[$prop] !== $curr[$prop]) {
                        $diff[$name] = true;
                        break;
                    }
                }
            } else if ($prev[$name] !== $curr[$name]) {
                $diff[$name] = true;
            }
        }

        return array_keys($diff);
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

            if (!$this->db->supportedBooleanValue() && $column->type() === PDO::PARAM_BOOL) {
                if ($column->multilingual()) {
                    foreach (LANGUAGES as $lang) {
                        $prop = "{$name}__{$lang}";

                        foreach ($rows as &$row) {
                            if ($row[$prop] !== null) {
                                $row[$prop] = !!$row[$prop];
                            }
                        }
                    }
                } else {
                    foreach ($rows as &$row) {
                        if ($row[$name] !== null) {
                            $row[$name] = !!$row[$name];
                        }
                    }
                }
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
                    if ($column->pseudo() || $column->readonly() || $column->traceable() === false) {
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

                if (!$diff) {
                    return;
                }

                $type = self::UPDATE;
                $curr = json_encode($diff, JSON_UNESCAPED_UNICODE);
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
        $statement->bindValue(3, defined('USER_ID') ? USER_ID : null, PDO::PARAM_INT);
        $statement->bindValue(4, defined('MEMBER_ID') ? MEMBER_ID : (defined('VENDOR_ID') ? VENDOR_ID : null), PDO::PARAM_INT);
        $statement->bindValue(5, constant('REMOTE_ADDR'), PDO::PARAM_STR);
        $statement->bindValue(6, $this->table->name(), PDO::PARAM_STR);
        $statement->bindValue(7, $dataId, PDO::PARAM_INT);
        $statement->bindValue(8, $prev, PDO::PARAM_STR);
        $statement->bindValue(9, $curr, PDO::PARAM_STR);
        $statement->execute();
    }

}
