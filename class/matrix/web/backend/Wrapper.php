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
        $columns = $this->columns();

        foreach ($this->table()->getColumns($columns) as $name => $column) {
            if ($column->multilingual()) {
                foreach (LANGUAGES as $language) {
                    $form = $this->wrapInput($column, $form, "{$name}__{$language}");
                }
            } else {
                $form = $this->wrapInput($column, $form, $name);
            }
        }

        if (is_array($columns)) {
            foreach ($this->table()->getColumns(false) as $name => $column) {
                if (array_search($name, $columns) === false) {
                    if ($column->multilingual()) {
                        foreach (LANGUAGES as $language) {
                            unset($form["{$name}__{$language}"]);
                        }
                    }

                    unset($form[$name]);
                }
            }
        }

        return $form;
    }

}
