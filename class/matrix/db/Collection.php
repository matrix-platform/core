<?php //>

namespace matrix\db;

class Collection {

    private $conditions = [];
    private $model;
    private $table;

    public function __construct($table) {
        $this->model = $table->model();
        $this->table = $table;
    }

    public function count() {
        return $this->model->count($this->conditions);
    }

    public function delete() {
        $list = $this->model->query($this->conditions);

        foreach ($list as $row) {
            if (!$this->model->delete($row)) {
                return false;
            }
        }

        return $list;
    }

    public function deleteOne() {
        $data = $this->get();

        if ($data) {
            $data = $this->model->delete($data);
        }

        return $data;
    }

    public function filter($conditions) {
        if (is_array($conditions)) {
            $this->conditions = array_merge($this->conditions, $conditions);
        } else if ($conditions instanceof Criterion) {
            $this->conditions[] = $conditions;
        } else {
            $this->conditions['id'] = $conditions;
        }

        return $this;
    }

    public function get($columns = false) {
        $list = $this->list($columns);

        return count($list) === 1 ? $list[0] : null;
    }

    public function group($name, $columns = false, $orders = true, $size = 0, $page = 1) {
        $grouping = [];

        foreach ($this->list($columns, $orders, $size, $page) as $row) {
            $grouping[$row[$name]][] = $row;
        }

        return $grouping;
    }

    public function increase($name, $value = 1) {
        $list = [];

        foreach ($this->model->query($this->conditions) as $row) {
            $row[$name] += $value;

            $row = $this->model->update($row);

            if ($row) {
                $list[] = $row;
            } else {
                return false;
            }
        }

        return $list;
    }

    public function list($columns = false, $orders = true, $size = 0, $page = 1) {
        return $this->model->query($this->conditions, $orders, $size, $page, $columns);
    }

    public function map($name = 'id', $columns = false, $orders = true, $size = 0, $page = 1) {
        $mapping = [];

        foreach ($this->list($columns, $orders, $size, $page) as $row) {
            $mapping[$row[$name]] = $row;
        }

        return $mapping;
    }

    public function pluck($column, $orders = true, $size = 0, $page = 1) {
        return array_column($this->list([$column], $orders, $size, $page), $column);
    }

    public function update($values) {
        $list = [];

        unset($values['id']);

        foreach ($this->model->query($this->conditions) as $row) {
            $row = $this->model->update(array_merge($row, $values));

            if ($row) {
                $list[] = $row;
            } else {
                return false;
            }
        }

        return $list;
    }

    public function updateOne($values) {
        $data = $this->get();

        if ($data) {
            unset($values['id']);

            $data = $this->model->update(array_merge($data, $values));
        }

        return $data;
    }

}
