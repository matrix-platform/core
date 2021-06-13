<?php //>

namespace matrix\db\pgsql;

use matrix\db\Dialect as DialectTrait;

class Dialect {

    use DialectTrait;

    public function makeDefaultExpression($expression, $default) {
        return "COALESCE({$expression}, {$default})";
    }

    public function makePager($command, $size, $page) {
        $offset = $size * ($page - 1);

        return "{$command} LIMIT {$size} OFFSET {$offset}";
    }

    public function makeRandom() {
        return 'RANDOM()';
    }

    public function quote($name) {
        return "\"{$name}\"";
    }

}
