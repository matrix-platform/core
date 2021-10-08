<?php //>

namespace matrix\db\pgsql;

use matrix\db\Dialect as DialectTrait;

class Dialect {

    use DialectTrait;

    public function makeDateTimeExpression($expression, $pattern) {
        $format = str_replace(['Y', 'm', 'd', 'H', 'i', 's'], ['YYYY', 'MM', 'DD', 'HH24', 'MI', 'SS'], $pattern);

        return "TO_CHAR({$expression}, '{$format}')";
    }

    public function makeDefaultExpression($expression, $default) {
        return "COALESCE({$expression}, {$default})";
    }

    public function makeImplodeExpression($expression, $separator) {
        return "ARRAY_TO_STRING({$expression}, '{$separator}')";
    }

    public function makePager($command, $size, $page) {
        $offset = $size * ($page - 1);

        return "{$command} LIMIT {$size} OFFSET {$offset}";
    }

    public function makeRandom() {
        return 'RANDOM()';
    }

    public function makeToArrayExpression($expression) {
        return "ARRAY_AGG({$expression} ORDER BY id)";
    }

    public function overlap($expression, $values) {
        return "{$expression} && STRING_TO_ARRAY(?, ',')::INTEGER[]";
    }

    public function quote($name) {
        return "\"{$name}\"";
    }

}
