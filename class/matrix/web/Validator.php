<?php //>

namespace matrix\web;

trait Validator {

    protected function validate($form) {
        return $this->validateForm($form);
    }

    protected function validateForm($form) {
        $errors = [];

        foreach ($this->rules() ?: [] as $name => $options) {
            $value = @$form[$name];

            if ($value === null) {
                $errors[] = ['name' => $name, 'type' => 'required'];
            } else {
                $type = validate($value, $options);

                if ($type !== true) {
                    $errors[] = ['name' => $name, 'type' => $type];
                }
            }
        }

        return $errors;
    }

}
