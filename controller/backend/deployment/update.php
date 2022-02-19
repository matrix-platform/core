<?php //>

use matrix\web\backend\ListController;

return new class() extends matrix\web\backend\Controller {

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $controller = routing('/backend/' . base64_urldecode($this->args()[0]), 'POST');

        if ($controller instanceof ListController) {
            if ($controller->permitted("{$controller->menuNode()}/update")) {
                return $this->sort($controller, @$form['id']);
            }

            return ['view' => '403.php'];
        } else {
            return ['view' => '404.php'];
        }
    }

    private function sort($controller, $list) {
        if (!$list || !is_array($list)) {
            return ['error' => 'error.data-not-found'];
        }

        $conditions = [];
        $table = $controller->table();
        $model = $table->model();
        $relation = $table->getParentRelation();

        if ($relation) {
            $args = $controller->args();
            $conditions[$relation['column']->name()] = $args ? $args[0] : null;
        }

        $data = $model->query($conditions);

        if (count($data) !== count($list)) {
            return ['error' => 'error.update-failed'];
        }

        $ranking = $table->ranking();
        $values = array_column($data, $ranking);
        $data = array_combine(array_column($data, $table->id()), $data);

        $unique = array_unique($values, SORT_NUMERIC);
        $reset = count($values) !== count($unique);

        foreach (array_combine($list, $values) as $id => $value) {
            $row = @$data[$id];

            if (!$row) {
                return ['error' => 'error.update-failed'];
            }

            if ($reset) {
                $row[$ranking] = null;

                $model->update($row);
            } else if ($row[$ranking] !== $value) {
                $row[$ranking] = $value;

                $model->update($row);
            }
        }

        return ['success' => true, 'view' => 'backend/save-success.php'];
    }

};
