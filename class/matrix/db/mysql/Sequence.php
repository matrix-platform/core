<?php //>

namespace matrix\db\mysql;

use matrix\db\Sequence as SequenceInterface;

class Sequence implements SequenceInterface {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function next($name) {
        $statement = $this->db->prepare("INSERT INTO {$name} (id) VALUES (0)");
        $statement->execute();

        return $this->db->lastId();
    }

    public function reset($name) {
        $this->db->prepare("TRUNCATE TABLE {$name}")->execute();
    }

}
