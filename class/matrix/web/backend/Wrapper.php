<?php //>

namespace matrix\web\backend;

trait Wrapper {

    private function wrapInput($column, $form, $name) {
        if (key_exists($name, $form)) {
            if ($column->multiple()) {
                if (is_array($form[$name])) {
                    $form[$name] = implode(',', $form[$name]);
                }
            }
        }

        return $form;
    }

    private function wrapModel($form) {
        foreach ($this->columns() ?: $this->table()->getColumns(false) as $name => $column) {
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
