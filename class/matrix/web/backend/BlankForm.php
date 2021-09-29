<?php //>

namespace matrix\web\backend;

use ReflectionMethod;

trait BlankForm {

    public function available() {
        if ($this->method() === 'POST') {
            $table = $this->table();
            $relation = $table->getParentRelation();

            if ($relation) {
                $info = pathinfo($this->name());
                $action = $info['basename'];

                if ($relation['self-referencing']) {
                    $pattern = preg_quote($info['dirname'], '/');
                    $relation = $table->getComposition($table);

                    return preg_match("/^{$pattern}(\/[\d]+\/{$relation['alias']})?\/{$action}(\/[\d]+)?$/", $this->path());
                } else {
                    $info = pathinfo($info['dirname']);
                    $pattern = preg_quote($info['dirname'], '/');

                    return preg_match("/^{$pattern}\/[\d]+\/{$info['basename']}\/{$action}(\/[\d]+)?$/", $this->path());
                }
            } else {
                $pattern = preg_quote($this->name(), '/');

                return preg_match("/^{$pattern}(\/[\d]+)?$/", $this->path());
            }
        }

        return false;
    }

    public function isRequired($column, $exists = true) {
        if ($column->required() && $column->default() === null) {
            if ($column->relation()) {
                return true;
            }

            $method = new ReflectionMethod($column, 'generate');

            if (basename($method->getFileName()) === 'Column.php') {
                return true;
            }
        }

        return false;
    }

    private function wrapParentId($form) {
        $relation = $this->table()->getParentRelation();

        if ($relation) {
            $args = $this->args();
            $form[$relation['column']->name()] = $args ? $args[0] : null;
        }

        return $form;
    }

}
