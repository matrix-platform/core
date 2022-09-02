<?php //>

namespace matrix\web\backend;

trait Validator {

    protected function validate($form) {
        $errors = [];

        foreach ($this->table()->getColumns($this->columns()) as $name => $column) {
            if ($column->multilingual()) {
                foreach (LANGUAGES as $language) {
                    $errors = $this->validateInput($errors, $column, $form, "{$name}__{$language}", $language);
                }
            } else {
                $errors = $this->validateInput($errors, $column, $form, $name);
            }
        }

        return $errors;
    }

    private function validateInput($errors, $column, $form, $name, $language = null) {
        $value = @$form[$name];

        if ($value === null) {
            if ($this->isRequired($column, key_exists($name, $form))) {
                $errors[] = ['name' => $name, 'type' => 'required'];
            }
        } else {
            $type = $column->validate($value);

            if ($type === true) {
                $alias = $column->association();

                if ($alias) {
                    $relation = $this->table()->getRelation($alias);
                    $condition = $relation['filter'];
                    $condition[] = $relation['target']->equal($value)->with($language);

                    if (!$relation['foreign']->model()->count($condition)) {
                        $errors[] = ['name' => $name, 'type' => 'not-found'];
                    }
                } else {
                    $options = $column->options();

                    if (is_array($options)) {
                        $token = is_bool($value) ? var_export($value, true) : $value;

                        foreach (explode(',', $token) as $key) {
                            if (!key_exists($key, $options)) {
                                $errors[] = ['name' => $name, 'type' => 'not-found'];
                                break;
                            }
                        }
                    }
                }

                if ($column->unique()) {
                    $condition = [$column->equal($value)->with($language)];

                    $id = $this->formId();

                    if ($id !== null) {
                        $condition[] = $this->table()->id->notEqual($id);
                    }

                    if ($this->table()->model()->count($condition)) {
                        $errors[] = ['name' => $name, 'type' => 'duplicated'];
                    }
                }
            } else {
                $errors[] = ['name' => $name, 'type' => $type];
            }
        }

        return $errors;
    }

}
