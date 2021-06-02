<?php //>

namespace matrix\db\criterion;

use Exception;
use matrix\db\Criterion;
use PDO;

class Version implements Criterion {

    protected $value;

    public function __construct($value) {
        $this->value = intval($value);
    }

    public function bind($statement, $bindings) {
        $bindings[] = $this->value;

        $statement->bindValue(count($bindings), $this->value, PDO::PARAM_INT);

        return $bindings;
    }

    public function make($dialect) {
        return '__version__ = ?';
    }

    public function with($language) {
        throw new Exception('Unsupported operation.');
    }

}
