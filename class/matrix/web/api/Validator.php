<?php //>

namespace matrix\web\api;

use matrix\web\backend\Validator as AbstractValidator;

trait Validator {

    use AbstractValidator {
        validate as validateModel;
    }

    public function isRequired($column, $exists = true) {
        return $column->required();
    }

    public function validation($error) {
        $message = @$error['message'];

        if (!$message) {
            $type = @$error['type'];

            if ($type) {
                $column = @$error['column'];
                $message = i18n("validation.{$type}", $type);

                if ($column) {
                    $message = i18n($column->i18n()) . ': ' . $message;
                }
            }
        }

        return $message;
    }

}
