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

}
