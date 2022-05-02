<?php //>

namespace matrix\web\backend;

trait Wrapper {

    protected function wrapInput($column, $form, $name) {
        if (key_exists($name, $form)) {
            if ($column->attachment()) {
                $form = $this->wrapFile($form, $name, $column->privilege());
            } else if ($column->multiple()) {
                if (is_array($form[$name])) {
                    $form[$name] = implode(',', array_filter($form[$name], function ($value) { return $value !== null; }));
                }
            }
        }

        return $form;
    }

    private function wrapModel($form) {
        foreach ($this->table()->getColumns($this->columns()) as $name => $column) {
            if ($column->multilingual()) {
                foreach (LANGUAGES as $language) {
                    $form = $this->wrapInput($column, $form, "{$name}__{$language}");
                }
            } else {
                $form = $this->wrapInput($column, $form, $name);
            }
        }

        return $form;
    }

}
