<?php //>

namespace matrix\db\mysql;

use matrix\db\Dialect as DialectTrait;

class Dialect {

    use DialectTrait;

    public function makeDateTimeExpression($expression, $pattern) {
        $format = str_replace(['Y', 'm', 'd', 'H', 'i', 's'], ['%Y', '%m', '%d', '%H', '%i', '%s'], $pattern);

        return "DATE_FORMAT({$expression}, '{$format}')";
    }

    public function makeDefaultExpression($expression, $default) {
        return "IFNULL({$expression}, {$default})";
    }

    public function makeImplodeExpression($expression, $separator) {
        return "REPLACE(REPLACE(REPLACE(JSON_UNQUOTE({$expression}), ' ', ''), '[', ''), ']', '')";
    }

    public function makeOrder($command, $columns, $orders) {
        $expressions = [];

        foreach ($orders as $name) {
            if ($name === '?') {
                $expressions[] = 'RAND()';
            } else {
                if ($name[0] === '-') {
                    $name = substr($name, 1);
                    $type = 'DESC';
                } else {
                    $type = 'ASC';
                }

                if (key_exists($name, $columns)) {
                    if ($columns[$name] === true) {
                        $name = $name . '__' . LANGUAGE;
                    }

                    $quoted = $this->quote($name);
                    $expressions[] = "{$quoted} IS NULL, {$quoted} {$type}";
                }
            }
        }

        if ($expressions) {
            $order = implode(', ', $expressions);

            return "{$command} ORDER BY {$order}";
        }

        return $command;
    }

    public function makePager($command, $size, $page) {
        $offset = $size * ($page - 1);

        return "{$command} LIMIT {$offset}, {$size}";
    }

    public function makeToArrayExpression($expression) {
        return "CAST(CONCAT('[', GROUP_CONCAT({$expression} ORDER BY id), ']') AS JSON)";
    }

    public function overlap($expression, $values) {
        return "JSON_OVERLAPS({$expression}, CAST(CONCAT('[', ?, ']') AS JSON))";
    }

    public function quote($name) {
        return "`{$name}`";
    }

}
