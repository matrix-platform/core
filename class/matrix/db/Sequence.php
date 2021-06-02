<?php //>

namespace matrix\db;

abstract class Sequence {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    abstract public function next($name);

}
