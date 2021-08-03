<?php //>

namespace matrix\db\pgsql;

use matrix\db\Sequence as SequenceInterface;

class Sequence implements SequenceInterface {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function next($name) {
        $statement = $this->db->prepare("SELECT NEXTVAL('{$name}')");
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function reset($name) {
        $this->db->prepare("ALTER SEQUENCE {$name} RESTART")->execute();
    }

}
