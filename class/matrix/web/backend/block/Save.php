<?php //>

namespace matrix\web\backend\block;

trait Save {

    protected function validate($form) {
        $errors = parent::validate($form);

        foreach ($this->module()['fields'] as $name => $field) {
            if ($field->multilingual()) {
                foreach (LANGUAGES as $language) {
                    $errors = $this->validateField($errors, $field, $form, "{$name}__{$language}");
                }
            } else {
                $errors = $this->validateField($errors, $field, $form, $name);
            }
        }

        return $errors;
    }

    protected function preprocess($form) {
        $extra = [];
        $table = $this->table();

        foreach ($this->module()['fields'] as $name => $field) {
            if (isset($table->{$name})) {
                if ($table->{$name}->multilingual()) {
                    foreach (LANGUAGES as $language) {
                        $form = $this->packField($form, $field, $form, "{$name}__{$language}");
                    }
                } else {
                    $form = $this->packField($form, $field, $form, $name);
                }
            } else {
                if ($field->multilingual()) {
                    foreach (LANGUAGES as $language) {
                        $extra = $this->packField($extra, $field, $form, "{$name}__{$language}");
                    }
                } else {
                    $extra = $this->packField($extra, $field, $form, $name);
                }
            }
        }

        $form['extra'] = $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : '{}';

        return $form;
    }

    private function packField($extra, $field, $form, $name) {
        $value = @$form[$name];

        if ($value !== null) {
            $extra[$name] = $field->convert($value);
        }

        return $extra;
    }

    private function validateField($errors, $field, $form, $name) {
        $value = @$form[$name];

        if ($value === null) {
            if ($field->required()) {
                $errors[] = ['name' => $name, 'type' => 'required'];
            }
        } else {
            $type = $field->validate($value);

            if ($type !== true) {
                $errors[] = ['name' => $name, 'type' => $type];
            }
        }

        return $errors;
    }

    private function wrapModule($form) {
        $table = $this->table();

        foreach ($this->module()['fields'] as $name => $field) {
            if (isset($table->{$name})) {
                $field->multilingual($table->{$name}->multilingual());
            }

            if ($field->multilingual()) {
                foreach (LANGUAGES as $language) {
                    $form = $this->wrapInput($field, $form, "{$name}__{$language}");
                }
            } else {
                $form = $this->wrapInput($field, $form, $name);
            }
        }

        return $form;
    }

}
