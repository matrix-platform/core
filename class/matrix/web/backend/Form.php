<?php //>

namespace matrix\web\backend;

use ReflectionMethod;

trait Form {

    public function isRequired($column, $exists = true) {
        if ($exists && $column->required() && !$column->readonly()) {
            if ($column->relation()) {
                return true;
            }

            $method = new ReflectionMethod($column, 'regenerate');

            if (basename($method->getFileName()) === 'Column.php') {
                return true;
            }
        }

        return false;
    }

}
