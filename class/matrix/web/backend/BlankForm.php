<?php //>

namespace matrix\web\backend;

use ReflectionMethod;

trait BlankForm {

    public function available() {
        if ($this->table()->getParentRelation()) {
            if ($this->method() === 'POST') {
                $info = pathinfo($this->name());
                $action = $info['basename'];

                $info = pathinfo($info['dirname']);
                $pattern = preg_quote($info['dirname'], '/');

                return preg_match("/^{$pattern}\/[\d]+\/{$info['basename']}\/{$action}$/", $this->path());
            }

            return false;
        }

        return parent::available();
    }

    public function isRequired($column, $exists = true) {
        if ($column->required() && $column->default() === null) {
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
            $form[$relation['column']->name()] = $this->args()[0];
        }

        return $form;
    }

}
